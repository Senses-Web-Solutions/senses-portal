<?php
namespace App\Traits;

use Closure;
use Illuminate\Support\Str;
use App\Support\ProcessGroup;
use App\Support\SensesUUID;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;
use Illuminate\Database\Schema\Blueprint;
use App\Events\Imports\ImportSenses6StepStarted;
use App\Events\Imports\ImportSenses6StepUpdated;
use Illuminate\Database\Eloquent\Relations\Relation;

trait DatabaseImport
{
    protected $chunkSize = 1100;
    protected $nextID = null;

    public function getTable()
    {
        return property_exists($this, 'table') ? $this->table : 'unknown';
    }

    public function getImportTable()
    {
        if (!property_exists($this, 'importTable') && property_exists($this, 'table')) {
            return $this->table;
        }
        return property_exists($this, 'importTable') ? $this->importTable : 'unknown';
    }

    public function getImportConnectionName()
    {
        return property_exists($this, 'importConnection') ? $this->importConnection : env('IMPORT_DB_CONNECTION', 'import_mysql');
    }

    public function getImportConnection()
    {
        return DB::connection($this->getImportConnectionName());
    }

    public function hasImportTable() {
        return Schema::connection($this->getImportConnectionName())->hasTable($this->getImportTable());
    }

    // public function mapClientRowColumns($row, $newRow, array $columns, Closure $callback = null) {
    //     $newRow = $this->mapRowColumns($row, $columns, softDeletes:false, uuid:false, mapArchived:false, checkExists:true);

    //     if($callback) {
    //         $newRow = $callback($newRow);
    //     }

    //     return array_merge($row, $newRow);
    // }

    public function mapRowColumns($row, array $columns, $softDeletes = true, $pivot = false, $uuid = true, $mapArchived = true, $checkExists = false)
    {
        $mappedRow = [];
        foreach ($columns as $newCol => $originalCol) {

            if($checkExists && !property_exists($row, $originalCol)) {
                continue; //move on
            }

            $mappedRow[$newCol] = $row->$originalCol;
        }

        if ($softDeletes) {
            $mappedRow['created_at'] = $row->created_at;
            $mappedRow['updated_at'] = $row->updated_at;

            if (!$pivot) {
                if($mapArchived) {
                    $mappedRow['hidden_at'] = $row->archived_at;
                    $mappedRow['hidden_by'] = $row->archived_by;

                    $mappedRow['deleted_at'] = $row->deleted_at;
                    $mappedRow['deleted_by'] = $row->deleted_by;
                }

                $mappedRow['created_by'] = $row->created_by;
                $mappedRow['updated_by'] = $row->updated_by;
            }


        }

        if($uuid && !$pivot) {
            $mappedRow['uuid'] = $this->generateUUID($row);
        }

        return $mappedRow;
    }

    public function generateUUID($row = null, $forceOwnClient = false) {

        if(is_null($row)) {
            return SensesUUID::generate();
        }

        if(property_exists($row, 'external_service_id')) {
            $table = Str::singular($this->getTable());
            $field = 'external_' . $table . '_id';
            $alternativeField = 'external_service_' . $table . '_id';

            $id = $row->id;

            if($this->isContractor()) {
                if(property_exists($row, $field)) {
                    $id = $row->$field;
                }

                if(property_exists($row, $alternativeField)) {
                    $id = $row->$alternativeField;
                }
            }

            $client = config('senses.client');
            if($this->isContractor()) {
                $client = 'lanesrail';  //TL6 only people contracting are lanesrail
            }

            return $this->generateClientUUID($client, $id ?? $row->id); //use own id as we may need this for parent systems
        }

        return SensesUUID::generate();
    }

    public function generateClientUUID($client, $id, $table = null) {
        $table = $table ??  $this->getTable();
        return $client .'-'.$table .'-'. $id;
    }

    public function getExternalClient($row) {

        return match(config('senses.client')) {
            'lanesrail' => $this->getLanesrailExternalClient($row),
            'beechenlea' => $this->getBeechenleaExternalClient($row),
            'yy' => $this->getYYExternalClient($row),
            'closebrothers' => $this->getCloseBrothersExternalClient($row),
            default => config('senses.client')
        };
    }

    public function getLanesrailExternalClient($row) {
        return match($row->external_service_id) {
            1 => 'beechenlea',
            2 => 'closebrothers',
            3 => 'yy',
            default => config('senses.client')
        };
    }

    public function getYYExternalClient($row) {
        return match($row->external_service_id) {
            1 => 'lanesrail',
            default => config('senses.client')
        };
    }

    public function getCloseBrothersExternalClient($row) {
        return match($row->external_service_id) {
            1 => 'lanesrail',
            default => config('senses.client')
        };
    }

    public function getBeechenleaExternalClient($row) {
        return match($row->external_service_id) {
            2 => 'lanesrail',
            default => config('senses.client')
        };
    }

    public function insertRow($row, $table = null)
    {
        return DB::table($table ?? $this->getTable())->insertGetId($row);
    }

    //Doesn't return ids
    public function insertRows($rows, $table = null)
    {
        if(is_array($rows) && !empty($rows)) {
            DB::table($table ?? $this->getTable())->insertOrIgnore($rows);
        }
    }

    public function importRows($callback, $excludeDeleted = false, $orderBy = 'id')
    {
        $query = $this->getImportQuery()->orderBy($orderBy);
        if ($excludeDeleted) {
            $query->whereNull('deleted_at');
        }
        $query->chunk(200, $callback);
    }

    public function importChunkRows($chunk, $callback, $excludeDeleted = false, $orderBy = 'id', $deletedAtField = 'deleted_at')
    {
        $this->insertRows($this->chunkRows($chunk, $callback, $excludeDeleted, $orderBy, $deletedAtField));
    }

    public function chunkRows($chunk, $callback, $excludeDeleted = false, $orderBy = 'id', $deletedAtField = 'deleted_at')
    {
        $query = $this->getImportQuery()->orderBy($orderBy);
        if ($excludeDeleted) {
            $query->whereNull($deletedAtField);
        }

        return $callback($query->forPage($chunk, $this->chunkSize)->get());
    }

    public function getImportQuery()
    {
        return $this->getImportQueryForTable($this->getImportTable());
    }

    public function getImportQueryForTable($table) {
        return DB::connection($this->getImportConnectionName())->table($table);
    }

    public function getNextAvailableID(bool $increment = true) {
        if(!$this->nextID) {
            $this->nextID = $this->getImportQuery()->orderBy('id', 'desc')->first()->id;
        }
        return $increment ? $this->nextID + 1 : $this->nextID;
    }

    public function getImportChunkTotal()
    {
        $count = $this->getImportQuery()->count();
        return intval(ceil($count / $this->chunkSize));
    }

    /* Data validation helpers */
    public function nullableDate($date)
    {
        return $date == '1900-01-01' ? null  : $date;
    }

    public function nullableString($value)
    {
        return !in_array(strtolower($value), [
            '',
            ' ',
            '.',
            '..',
            '...',
            'n/a',
            '0',
            'tbc',
            '1',
            '?',
            '??'
        ]) ? $value : null;
    }

    public function nullableEmail($value)
    {
        $value = $this->nullableString($value);

        if ($value) {
            $value = Str::startsWith($value, 'tbc@') ? null : $value;
            if ($value) {
                $value = Str::endsWith($value, '@example.com') ? null : $value;
            }
        }

        return $value;
    }

    /* ID Tracking,
    some data has changed id, functions to help store/retrieve based on new/old ids
    */
    public function dropImportLookupTable()
    {
        Schema::dropIfExists('import_lookups');
    }

    public function createImportLookupTable($forceDrop = false)
    {
        if ($forceDrop) {
            $this->dropImportLookupTable();
        }
        Schema::create('import_lookups', function (Blueprint $table) {
            $table->bigInteger('id')->index();
            $table->bigInteger('prev_id')->index();
            $table->string('table');
            $table->string('prev_table')->index();
        });
    }


    public function insertLookupRow($id, $prevID, $table = null, $prevTable = null)
    {
        $this->getLookupTable()->insert([
            'id' => $id,
            'prev_id' => $prevID,
            'table' => $table ?? $this->getTable(),
            'prev_table' => $prevTable ?? $this->getImportTable(),
        ]);
    }

    public function insertLookupRows($rows, $table =  null, $prevTable = null)
    {
        foreach ($rows as $row) {
            $this->insertLookupRow($row[0], $row[1], $table, $prevTable);
        }
    }

    public function lookupPreviousIDExists($prevID, $prevTable = null)
    {
        return $this->getLookupTable()->where('prev_id', $prevID)->where('prev_table', $prevTable)->exists();
    }

    public function lookupIDExists($id, $table)
    {
        return $this->getLookupTable()->where('table', $table)->where('id', $id)->exists();
    }

    public function getFromLookup($prevID, $prevTable)
    {
        return $this->getLookupTable()->where('prev_table', $prevTable)->where('prev_id', $prevID)->first();
    }

    public function getIDFromLookup($prevID, $prevTable) {
        return $this->getFromLookup($prevID, $prevTable)?->id;
    }

    public function getLookupTable()
    {
        return DB::table('import_lookups');
    }

    public function queueChunkActions(string $actionClass)
    {
        $chunkTotal = $this->getImportChunkTotal();
        foreach (range(1, $chunkTotal) as $chunk) {
            (new $actionClass())
            // ->onQueue()
            ->execute($chunk);
        }
    }


    public function resolveRelation($relation, $strict = true) {
        $legacyRelationMap = [
            'App\Models\ControlledDocument' => 'document',
            'App\Models\BasicEarningLine' => 'revenue',
            'App\Models\TaskState' => 'status',
            'App\Models\CostLine' => 'revenue',
            'App\Models\Todo' => 'todo',
            'App\Models\AssetInspectionSheet' => 'form',
            'App\Models\AssetFault' => 'fault',
            'App\Models\EarningRate' => 'rate',
            'App\Models\EarningType' => 'revenue_type',
            'App\Models\CostType' => 'revenue_type',
            'App\Models\Contract' => 'project',
            'App\Models\AssetUnavailability' => 'unavailabilities',
        ];

        if(isset($legacyRelationMap[$relation])) {
            return $legacyRelationMap[$relation];
        }
        else {
            $flipped = array_flip(Relation::morphMap());
            if($strict && !isset($flipped[$relation])) {
                throw new \Exception("resolveRelation can't resolve " . $relation);
            }

            if(isset($flipped[$relation])) {
                return $flipped[$relation];
            }
            return null;
        }
    }

    public function resolveRelationID($relation, $id) {

        return match($relation) {
            'App\Models\Document' => $this->getIDFromLookupWithCheck($id, 'documents'),
            'App\Models\BasicEarningLine' => $this->getIDFromLookupWithCheck($id, 'basic_earning_lines'),
            'App\Models\TagGroups' => $this->getIDFromLookupWithCheck($id, 'tag_groups'),
            'App\Models\Tag' => $this->getIDFromLookupWithCheck($id, 'tags'),
            'App\Models\TaskState' => $this->getIDFromLookupWithCheck($id, 'task_states'),
            'App\Models\CostLine' => $this->getIDFromLookupWithCheck($id, 'cost_lines'),
            'App\Models\AssetInspectionSheetStructure' => $this->getIDFromLookupWithCheck($id, 'asset_inspection_sheet_structures'),
            'App\Models\CostType' => $this->getIDFromLookupWithCheck($id, 'cost_types'),
            'App\Models\AssetUnavailability' => $this->getIDFromLookupWithCheck($id, 'asset_unavailabilities'),
            default => $id
        };
    }

    public function getIDFromLookupWithCheck($id, $table) {
        $found = $this->getIDFromLookup($id, $table);
        if(!$found) {
            echo 'Could not resolve relation for ' . $table . ' with ID ' . $id . PHP_EOL;
        }

        return $found;
    }


    public function getExtraColumns($table) {
        $columns = [];
        if(str_contains($this->getImportConnectionName(), 'mysql')) {

            $mysqlColumns = $this->getImportConnection()->select('SHOW COLUMNS FROM ' . $table);

            $columns = collect($mysqlColumns)->map(function ($column) use(&$table) {
                return "{$table}.{$column->Field} as {$table}_{$column->Field}";
            })->toArray();
        }
        else if(str_contains($this->getImportConnectionName(), 'pgsql')) {

            $pgsqlColumns = $this->getImportConnection()->select("SELECT * FROM information_schema.columns WHERE table_schema = 'senses' AND table_name = '{$table}';");

            $columns = collect($pgsqlColumns)->map(function ($column) use(&$table) {
                return "{$table}.{$column->column_name} as {$table}_{$column->column_name}";
            })->toArray();
        }

        return $columns;
    }

    public function validCoordinate($lat, $lng) {
        if(!is_numeric($lat)){
            return false;
        }

        if(!is_numeric($lng)){
            return false;
        }

        $lat = floatval($lat);
        $lng = floatval($lng);

        // logger([$lat, $lng, ($lng > -9)]);
        return ($lng > -9) && ($lng < 2); //just test lngs bound for now, as any issues will break import anyway.
    }

    public function queueCursorChunkActions(string $actionClass, $field = 'id')
    {
        $idTotal = $this->getImportLastID($field);

        for($id = 0; $id <= $idTotal; $id += $this->chunkSize) {

            (new $actionClass())
            // ->onQueue()
            ->execute($id);
        }

    }

    public function parallelChunkActions(string $actionClass, $field = 'id') {
        $idTotal = $this->getImportLastID($field);

        $chunks = ceil($idTotal / $this->chunkSize);
        $processGroup = new ProcessGroup();
        $processGroup->chunk($chunks, function($chunk) use($actionClass) {
            // echo 'running chunk ' . $chunk * $this->chunkSize . PHP_EOL;
            return new Process(['php', 'artisan', 'import:senses6_chunk', $actionClass, $chunk * $this->chunkSize]);
        });
    }

    public function parallelMultiChunkActions(string $actionClass, $field = 'id') {
        $idTotal = $this->getImportLastID($field);

        $chunks = ceil($idTotal / $this->chunkSize);
        $processGroup = new ProcessGroup();
        $processGroup->multiChunk($chunks, function($chunk,$totalChunks) use($actionClass) {
            // echo 'running chunk ' . $chunk * $this->chunkSize . PHP_EOL;
            return new Process(['php', 'artisan', 'import:senses6_multi_chunk', $actionClass, $chunk, $totalChunks, $this->chunkSize]);
        });
    }

    public function getImportLastID($field = 'id') {
        return $this->getImportQuery()->orderBy($field, 'desc')->select($field)->first()?->$field;
    }

    public function importCursorChunkRows($startingID, $callback, $excludeDeleted = false, $deletedAtField = 'deleted_at')
    {
        $this->insertRows($this->cursorChunkRows($startingID, $callback, $excludeDeleted, $deletedAtField));
    }

    public function cursorChunkRows($startingID, $callback, $excludeDeleted = false, $deletedAtField = 'deleted_at')
    {
        $query = $this->getImportQuery();
        if ($excludeDeleted) {
            $query->whereNull($deletedAtField);
        }
        return $callback($query->forPageAfterId($this->chunkSize, $startingID, $this->getImportTable() . '.id')->get());
    }

    public function migrationFolderPath() {
        return storage_path('app/migrations/' . config('senses.client'));
    }

    public function contractorParentTasksLookupPath() {
        return $this->migrationFolderPath() . '/contractor_parent_tasks_lookup.csv';
    }

    public function isNotContractor() {
        return !$this->isContractor();
    }

    public function isContractor() {
        return in_array(config('senses.client', null), ['closebrothers', 'yy', 'beechenlea']);
    }
}
