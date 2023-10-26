<?php

namespace App\Support;

use Throwable;
use ZipArchive;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Notifications\BackupHasFailed;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Notification;

class Backup  {

    public $path;
    public $command;
    public $partial = false;

    public function __construct(Command $command = null, bool $partial = false)
    {
        $this->command = $command;
        $this->partial = $partial;
        $this->path = storage_path('senses-backups/temp/' . $this->clientName() . $this->dumpName() . now()->format('Y-m-d_H-i-s'));
    }

    public static function isEnabled() {
        return env('BACKUPS_ENABLED', true);
    }

    public static function isDisabled() {
        return !self::isEnabled();
    }

    public function clientName() {
        return config('senses.client');
    }

    public function dumpName() {
        return $this->partial ? '_backup_partial_' : '_backup_full_';
    }

    public function zipName() {
        return $this->partial ? '_backup_PARTIAL_' : '_backup_FULL_';
    }

    public function run(bool $uploadDatabase = true, bool $pruneBackups = true) {
        try {
            if(self::isDisabled()) {
                $this->log('Backups are disabled in ENV');
                return;
            }
            $this->setupDirectories();
            $backedUp = $this->backupDatabase();
            if(!$backedUp) {
                return;
            }

            if($uploadDatabase) {
                $this->uploadDatabase($this->compressBackup());
            }

            if($pruneBackups) {
                $this->pruneOldBackups();
            }

            $this->log('Backup completed');
        }
        catch(Throwable $exception) {
            $this->handleFailure($exception);
            throw $exception;
        }
    }

    public function log($data)
    {

        if (is_string($data)) {
            $line =  "[" . now()->format('Y-m-d_H-i-s') . "]: " . $data;
        }
        else {
            $line =  "[" . now()->format('Y-m-d_H-i-s') . "]: " . ((string) print_r($data, true));
        }

        $this->command?->info($line);

        logger()->channel('backups')->info($line);
    }

    public function pruneOldBackups()
    {
        $this->log('Pruning old backups.');

        $files = collect(File::files(storage_path('senses-backups/finished')));

        $backups = $files->filter(function($item) {
            $item = str_replace(Str::beforeLast($item, '/'), '', $item);
            return Str::startsWith($item,  '/' . $this->clientName() . $this->zipName());
        });

        $pruned = 0;

        foreach ($backups as $index => $backup) {
            $backup = str_replace(storage_path('senses-backups/finished/' . $this->clientName() . $this->zipName()), '', $backup);
            $backup = str_replace('.zip', '', $backup);

            $d = Carbon::createFromFormat('Y-m-d_H-i-s', $backup);

            if($d->isBefore(now()->subDays(1))) {
                File::delete($backups[$index]);
                $pruned++;
            }
        }

        $this->log("Pruned {$pruned} backups.");
    }

    public function notify($exception) {

        Notification::route('slack', 'https://hooks.slack.com/services/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')
            ->route('mail', 'matt@senses.co.uk')
            ->route('mail', 'rob@senses.co.uk')
            ->notify(new BackupHasFailed());
    }

    public function setupDirectories()
    {
        if (!is_dir(storage_path('senses-backups'))) {
            mkdir(storage_path('senses-backups'));
        }

        if (!is_dir(storage_path('senses-backups/temp'))) {
            mkdir(storage_path('senses-backups/temp'));
        }

        if (!is_dir(storage_path('senses-backups/finished'))) {
            mkdir(storage_path('senses-backups/finished'));
        }

        if (!is_dir($this->path)) {
            mkdir($this->path);
        }
    }

    public function backupDatabase() {
        if($this->partial) {
            return $this->backupPartialDatabase();
        }
        else {
            return $this->backupFullDatabase();
        }
    }

    public function backupFullDatabase() : bool
    {
        $command = [
            'pg_dump',
            '--blobs',
            '--dbname=' . config('database.connections.pgsql.database'),
            '--host=' . config('database.connections.pgsql.host'),
            // '--exclude-table-data \'*.app_logs\' --exclude-table-data \'*.email_logs\' --exclude-table-data \'*.email_log\' --exclude-table-data \'*.error_logs\' --exclude-table-data \'*.app_conflicts\'',
            '--jobs=4',
            '--file=' . $this->path,
            '-Z 5',
            '--format=directory',
            "--username=" . config('database.connections.pgsql.username')
        ];

        $this->log('Preparing to perform full backup to ' . $this->path);

        // Create credentials file.
        $contents = [
            config('database.connections.pgsql.host'),
            config('database.connections.pgsql.port', 5432),
            config('database.connections.pgsql.database'),
            config('database.connections.pgsql.username'),
            config('database.connections.pgsql.password'),
        ];

        $credentialsContents = implode(':', $contents);

        $tempFileHandle = tmpfile();
        fwrite($tempFileHandle, $credentialsContents);

        $temporaryCredentialsFile = stream_get_meta_data($tempFileHandle)['uri'];

        // merge current environment variables with our custom ones or we lose access to pg_dump on win
        $process = new Process($command, null, array_merge(getenv(), [
            'PGPASSFILE' => $temporaryCredentialsFile
        ]));

        $process->setTimeout(2700);
        $process->setIdleTimeout(2700);

        $this->log('Backup started.');

        $backupStart = now();

        $process->mustRun();

        $this->log('Database backup complete, took ' . $backupStart->diffInSeconds(now()) . ' seconds.');
        return true;
    }

    public function backupPartialDatabase() : bool {

        $this->log('Partial backup started');

        $backupStart = now();

        // Get all tables.....
        $tables = DB::select("SELECT *
        FROM pg_catalog.pg_tables
        WHERE schemaname != 'pg_catalog' AND
            schemaname != 'information_schema' AND
                schemaname != 'public';");

        $sqlFile = '';

        // loop through and get all things which have changed since 00:00:00 today. (note you could change this to time of last successful full backup.....)
        foreach ($tables as $table) {
            $table = $table->tablename;

            $TIME_START_OF_DAY = now()->startOfDay()->format('Y-m-d H:i:s');

            $query = "SELECT * FROM {$table} where pg_xact_commit_timestamp({$table}.xmin) > '{$TIME_START_OF_DAY}' ORDER BY pg_xact_commit_timestamp({$table}.xmin) DESC";
            // $countQuery = "SELECT COUNT(*) FROM {$table} where pg_xact_commit_timestamp({$table}.xmin) > '{$TIME_START_OF_DAY}'";

            // $result = DB::statement($countQuery);
            $count = DB::table($table)->whereRaw("pg_xact_commit_timestamp({$table}.xmin) > '{$TIME_START_OF_DAY}'")->count();

            // run the query
            if ($count) {
                $response = DB::select($query);
                $csv = fopen("{$this->path}/{$table}.csv", 'w');
                fputcsv($csv, array_keys((array) $response[0]));

                foreach ($response as $row) {
                    fputcsv($csv, array_values((array) $row));
                }

                fclose($csv);
            }
        }

        if (!count(File::files($this->path))) {
            $this->log('No changes. Not backing up.');
            return false;
        }

        // file_put_contents($path . '/restore.sql', $sqlFile);

        $this->log("Backup complete, took " . $backupStart->diffInSeconds(Carbon::now()) . ' seconds.');
        return true;
    }

    public function compressBackup()
    {
        $zipPath = storage_path('senses-backups/finished/' . $this->clientName() . $this->zipName() . now()->format('Y-m-d_H-i-s') . '.zip');

        $this->log('Compressing backup');


        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE);
        $files = File::files($this->path);
        foreach ($files as $file) {
            $zip->addFile($file, str_replace('/', '', str_replace(Str::beforeLast($file, '/'), '', $file)));
        }

        $zip->close();

        $this->log('Backup compressed successfully.');

        File::deleteDirectory($this->path);

        $this->log('Zip Process Complete');

        return $zipPath;
    }

    public function uploadDatabase(string $zipPath)
    {
        $command = [
            "rclone",
            "copy",
            "--verbose",
            $zipPath,
            "GoogleDrive:/SensesAndFootprint/" . $this->clientName() . "/Databases"
        ];
        $process = new Process($command, null, getenv());
        $process->setTimeout(2700);
        $process->setIdleTimeout(2700);

        $this->log('Uploading to Google Drive');

        $process->mustRun();

        // Remove the uploaded zip
        // File::delete($path);

        $this->log('Upload complete.');
    }

    public function handleFailure(Throwable $exception) {
        rescue(fn() =>  $this->notify($exception));
        $this->log($exception->getMessage());
        $this->log((array) $exception);
    }
}
