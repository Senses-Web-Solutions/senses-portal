<?php
namespace App\Traits;
//Literally https://github.com/calebporzio/sqlite
//modified to only do from sqlite (aka cache mode in that package)
//we also need one connection per instance of model as its different files

use Closure;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

trait SqliteModel
{
    protected $sqliteConnection;
    protected $sqliteFilename;
    protected $sqliteFileDirectory;



    public static function instance(string $sqliteFilename, string $sqliteFileDirectory = null) {
        $report = new static();
        if(!$sqliteFileDirectory) {
            if(method_exists(static::class, 'getDefaultSqliteFileDirectory')) {
                $sqliteFileDirectory = static::getDefaultSqliteFileDirectory();
            }
            else {
                $sqliteFileDirectory = storage_path('app/sqlites');
            }
        }

        $report->init($sqliteFilename, $sqliteFileDirectory);
        return $report;
    }

    public function init(string $sqliteFilename, string $sqliteFileDirectory = null) {
        $this->sqliteFilename = $sqliteFilename;
        $this->sqliteFileDirectory = $sqliteFileDirectory;
        $this->setSqliteConnection($this->sqliteFileDirectory.'/'. $this->sqliteFilename);
    }

    public function getSqliteFileName() {
        return $this->sqliteFilename;
    }

    public function getSqliteFileDirectory() {
        return $this->sqliteFileDirectory;
    }

    public function getSchema()
    {
        return $this->schema ?? [];
    }

    public function getConnection()
    {
        return $this->sqliteConnection;
    }

    protected function setSqliteConnection($database)
    {
        $config = [
            'driver' => 'sqlite',
            'database' => $database,
        ];

        $this->sqliteConnection = app(ConnectionFactory::class)->make($config);
        $this->setConnection($this->sqliteConnection);
        // app('config')->set('database.connections.sqlite', $config);
    }

    public function migrate()
    {
        // $rows = $this->getRows();
        // $tableName = $this->getTable();

        // if (count($rows)) {
        //     $this->createTable($tableName, $rows[0]);
        // } else {
        //     $this->createTableWithNoData($tableName);
        // }

        // foreach (array_chunk($rows, $this->getsqliteInsertChunkSize()) ?? [] as $inserts) {
        //     if (!empty($inserts)) {
        //         static::insert($inserts);
        //     }
        // }
    }

    public function createTable(string $tableName, $firstRow)
    {
        $this->createTableSafely($tableName, function ($table) use ($firstRow) {
            // Add the "id" column if it doesn't already exist in the rows.
            if ($this->incrementing && ! array_key_exists($this->primaryKey, $firstRow)) {
                $table->increments($this->primaryKey);
            }

            foreach ($firstRow as $column => $value) {
                switch (true) {
                    case is_int($value):
                        $type = 'integer';
                        break;
                    case is_numeric($value):
                        $type = 'float';
                        break;
                    case is_string($value):
                        $type = 'string';
                        break;
                    case is_object($value) && $value instanceof \DateTime:
                        $type = 'dateTime';
                        break;
                    default:
                        $type = 'string';
                }

                if ($column === $this->primaryKey && $type == 'integer') {
                    $table->increments($this->primaryKey);
                    continue;
                }

                $schema = $this->getSchema();

                $type = $schema[$column] ?? $type;

                $table->{$type}($column)->nullable();
            }

            if ($this->usesTimestamps() && (! in_array('updated_at', array_keys($firstRow)) || ! in_array('created_at', array_keys($firstRow)))) {
                $table->timestamps();
            }
        });
    }

    public function createTableWithNoData(string $tableName)
    {
        $this->createTableSafely($tableName, function ($table) {
            $schema = $this->getSchema();

            if ($this->incrementing && ! in_array($this->primaryKey, array_keys($schema))) {
                $table->increments($this->primaryKey);
            }

            foreach ($schema as $name => $type) {
                if ($name === $this->primaryKey && $type == 'integer') {
                    $table->increments($this->primaryKey);
                    continue;
                }

                $table->{$type}($name)->nullable();
            }

            if ($this->usesTimestamps() && (! in_array('updated_at', array_keys($schema)) || ! in_array('created_at', array_keys($schema)))) {
                $table->timestamps();
            }
        });
    }

    protected function createTableSafely(string $tableName, Closure $callback)
    {
        /** @var \Illuminate\Database\Schema\SQLiteBuilder $schemaBuilder */
        $schemaBuilder = static::resolveConnection()->getSchemaBuilder();

        try {
            $schemaBuilder->create($tableName, $callback);
        } catch (QueryException $e) {
            if (Str::contains($e->getMessage(), 'already exists (SQL: create table')) {
                // This error can happen in rare circumstances due to a race condition.
                // Concurrent requests may both see the necessary preconditions for
                // the table creation, but only one can actually succeed.
                return;
            }

            throw $e;
        }
    }

    public function usesTimestamps()
    {
        // Override the Laravel default value of $timestamps = true; Unless otherwise set.
        return (new \ReflectionClass($this))->getProperty('timestamps')->class === static::class
            ? parent::usesTimestamps()
            : false;
    }

    public function getsqliteInsertChunkSize() {
        return $this->sqliteInsertChunkSize ?? 100;
    }
}