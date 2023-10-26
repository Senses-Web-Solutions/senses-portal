<?php

namespace App\Console\Commands;

use Exception;
use Throwable;
use ZipArchive;
use App\Enums\Format;
use App\Support\Backup;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Notifications\BackupHasFailed;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SensesBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'senses-backup:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a full backup of the database.';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $backup = new Backup($this);
        $backup->run();

        return 0;
    }




}
