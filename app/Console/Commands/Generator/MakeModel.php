<?php

namespace App\Console\Commands\Generator;

use Carbon\Carbon;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueueableAction\ActionMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeModel extends GeneratorCommand
{
    protected $name = 'make:senses_model';

    protected $description = 'Create a new model';

    protected $type = 'Model';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/generator/model.stub');
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();
        return array_merge($options, [
            ['model', null, InputOption::VALUE_REQUIRED, 'Model'],
            ['path', null, InputOption::VALUE_REQUIRED, 'Path'],
            ['force', null, InputOption::VALUE_REQUIRED, 'Force'],
        ]);
    }

    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $stub = str_replace(['{{ model }}', '{{model}}'], $this->option('model'), $stub); //assignment-group
        $stub = str_replace(['{{ pluralModel }}', '{{pluralModel}}'], Str::plural($this->option('model')), $stub); //assignment-groups
        $stub = str_replace(['{{ camelModel }}', '{{camelModel}}'], Str::camel($this->option('model')), $stub); //assignmentGroup
        $stub = str_replace(['{{ camelPluralModel }}', '{{camelPluralModel}}'], Str::plural(Str::camel($this->option('model'))), $stub); //assignmentGroups
        $stub = str_replace(['{{ ucCamelModel }}', '{{ucModel}}'], ucfirst(Str::camel($this->option('model'))), $stub); //AssignmentGroup
        $stub = str_replace(['{{ ucCamelPluralModel }}', '{{ucPluralModel}}'], ucfirst(Str::plural(Str::camel($this->option('model')))), $stub); //AssignmentGroups
        $stub = str_replace(['{{ snakeModel }}', '{{snakeModel}}'], str_replace('-', '_', $this->option('model')), $stub); //assignment_group
        $stub = str_replace(['{{ snakePluralModel }}', '{{snakePluralModel}}'], str_replace('-', '_', Str::plural($this->option('model'))), $stub); //assignment_groups
        $stub = str_replace(['{{ sentenceModel }}', '{{sentenceModel}}'], str_replace('-', ' ', $this->option('model')), $stub); //assignment group
        $stub = str_replace(['{{ sentencePluralModel }}', '{{sentencePluralModel}}'], str_replace('-', ' ', Str::plural($this->option('model'))), $stub); //assignment groups
        $stub = str_replace(['{{ titleModel }}', '{{titleModel}}'], ucwords(str_replace('-', ' ', $this->option('model'))), $stub); //Assignment Group
        $stub = str_replace(['{{ titlePluralModel }}', '{{titlePluralModel}}'], ucwords(str_replace('-', ' ', Str::plural($this->option('model')))), $stub); //Assignment Groups
        $stub = str_replace(['{{ generationDate }}', '{{generationDate}}'], Carbon::now()->format('d-m-Y H:i:s'), $stub);

        $this->buildUsingJson($stub);

        return $stub;
    }

    protected function buildUsingJson(&$stub)
    {
        $file = file_get_contents("json_files/" . $this->option('model') . ".json");
        $jsonData = json_decode($file, true);

        //Revision Model & Class
        $revisionPath = '';
        $revisionModel = '';
        if (isset($jsonData['revisionModel'])) {
            $revisionPath = PHP_EOL . 'use App\\Models\\' . $jsonData['revisionModel'] . ';';
            $revisionModel = 'protected $revisionModel = ' . $jsonData['revisionModel'] . '::class;';
        }
        $stub = str_replace(['{{ revisionPath }}', '{{revisionPath}}'], $revisionPath, $stub);
        $stub = str_replace(['{{ revision }}', '{{revision}}'], $revisionModel, $stub);

        //Traits paths & uses
        $traitPaths = '';
        $traits = '';
        if (isset($jsonData['traits']) && !empty($jsonData['traits'])) {
            $traits = ', ';
            foreach ($jsonData['traits'] as $trait) {
                $traitPaths .= 'use App\\Traits\\' . $trait . ';' . PHP_EOL;
            }
            $traits .= implode(', ', $jsonData['traits']);
        }
        $stub = str_replace(['{{ traitPaths }}', '{{traitPaths}}'], $traitPaths, $stub);
        $stub = str_replace(['{{ traits }}', '{{traits}}'], $traits, $stub);

        //Fillables
        $fillables = 'protected $fillable = [' . PHP_EOL . "\t\t" . '\'app_id\',' . PHP_EOL . "\t\t";
        $fillablesArray = [];
        if ($jsonData['fillables'] && !empty($jsonData['fillables'])) {
            foreach ($jsonData['fillables'] as $fillable) {
                array_push($fillablesArray, "'" . $fillable . "'");
            }
        }
        $fillableString = implode(', ' . PHP_EOL . "\t\t", $fillablesArray);
        $fillables .= $fillableString . PHP_EOL . "\t" . '];';

        $stub = str_replace(['{{ fillables }}', '{{fillables}}'], $fillables, $stub);

        //Casts
        $accessCasts = "'locked_at' => DateTime::class," . PHP_EOL . "\t\t'created_at' => DateTime::class," . PHP_EOL . "\t\t'updated_at' => DateTime::class," . PHP_EOL . "\t\t'deleted_at' => DateTime::class," . PHP_EOL . "\t\t'hidden_at' => DateTime::class," . PHP_EOL . "\t\t";
        $casts = 'protected $casts = [' . PHP_EOL . "\t\t" . $accessCasts;
        $castsArray = [];
        foreach ($jsonData['casts'] as $field => $type) {
            if ($type === 'datetime') {
                array_push($castsArray, "'" . $field . "' => DateTime::class");
            } else if ($type === 'date') {
                array_push($castsArray, "'" . $field . "' => Date::class");
            } else {
                array_push($castsArray, "'" . $field . "' => '" . $type . "'");
            }
        }
        $castsString = implode(', ' . PHP_EOL . "\t\t", $castsArray);
        $casts .= $castsString . PHP_EOL . "\t" . '];';

        $stub = str_replace(['{{ casts }}', '{{casts}}'], $casts, $stub);

        //Allowed Fields
        $allowedSorts = [];
        foreach ($jsonData['allowedSorts'] as $index => $fillable) {
            if ($fillable != "'id'" && $fillable != 'id') {
                $allowedSorts[$index] = "'" . $fillable . "'";
            }
        }
        $allowedSortsString = implode(', ', $allowedSorts);

        $stub = str_replace(['{{ allowedSorts }}', '{{allowedSorts}}'], $allowedSortsString, $stub);

        //Allowed Embeds
        foreach ($jsonData['allowedEmbeds'] as $index => $fillable) {
            $jsonData['allowedEmbeds'][$index] = "'" . $fillable . "'";
        }
        $allowedEmbedsString = implode(', ', $jsonData['allowedEmbeds']);

        $stub = str_replace(['{{ allowedEmbeds }}', '{{allowedEmbeds}}'], $allowedEmbedsString, $stub);

        //Allowed Fields
        $allowedFields = [];
        foreach ($jsonData['allowedFields'] as $index => $fillable) {
            if ($fillable != "'id'" && $fillable != 'id') {
                $allowedFields[$index] = "'" . $fillable . "'";
            }
        }
        $allowedFieldsString = implode(', ', $allowedFields);

        $stub = str_replace(['{{ allowedFields }}', '{{allowedFields}}'], $allowedFieldsString, $stub);

        //Allowed Filters
        $allowedFilters = [];
        foreach ($jsonData['allowedFields'] as $index => $field) {
            if ($field != "'id'" && $field != 'id') {
                $allowedFilters[$index] = "'" . $field . "'" . " => 'text'";
            }
        }
        $allowedFiltersString = implode(', ' . PHP_EOL . "\t\t\t", $allowedFilters);

        $stub = str_replace(['{{ allowedFilters }}', '{{allowedFilters}}'], $allowedFiltersString, $stub);

        // relationships
        $relationships = [];

        //hasOne
        $hasOnes = '';
        foreach ($jsonData['hasOne'] as $hasOne) {
            $field = is_string($hasOne) ? $hasOne : $hasOne['relation'];
            $model = is_string($hasOne) ? $hasOne : $hasOne['model'];
            $foreignKey = (is_string($hasOne) ? null : array_key_exists('foreignKey', $hasOne)) ? $hasOne['foreignKey'] : null; //todo this syntax is hard to follow, had to add extra () as it wasn't php8 compatable
            $relationship = $this->getRelationshipFunction($field, 'hasOne', $hasOnes, $model, $foreignKey);
            $relationships[$field] = $relationship;
        }

        // $stub = str_replace(['{{ hasOne }}', '{{hasOne}}'], $hasOnes, $stub);

        //belongsTo
        $belongsTos = '';
        foreach ($jsonData['belongsTo'] as $belongsTo) {
            $field = is_string($belongsTo) ? $belongsTo : $belongsTo['relation'];
            $model = is_string($belongsTo) ? $belongsTo : $belongsTo['model'];
            $foreignKey = (is_string($belongsTo) ? null : array_key_exists('foreignKey', $belongsTo)) ? $belongsTo['foreignKey'] : null;
            $relationship = $this->getRelationshipFunction($field, 'belongsTo', $belongsTos, $model, $foreignKey);
            $relationships[$field] = $relationship;
        }
        // $stub = str_replace(['{{ belongsTo }}', '{{belongsTo}}'], $belongsTos, $stub);

        //belongsToMany
        $belongsToManys = '';
        foreach ($jsonData['belongsToMany'] as $belongsToMany) {
            $field = is_string($belongsToMany) ? $belongsToMany : $belongsToMany['relation'];
            $model = is_string($belongsToMany) ? $belongsToMany : $belongsToMany['model'];
            $foreignKey = (is_string($belongsToMany) ? null : array_key_exists('foreignKey', $belongsToMany)) ? $belongsToMany['foreignKey'] : null;
            $relationship = $this->getRelationshipFunction($field, 'belongsToMany', $belongsToManys, $model, $foreignKey);
            $relationships[$field] = $relationship;
        }
        // $stub = str_replace(['{{ belongsTo }}', '{{belongsTo}}'], $belongsTos, $stub);

        //hasMany
        $hasManys = '';
        foreach ($jsonData['hasMany'] as $hasMany) {
            $field = is_string($hasMany) ? $hasMany : $hasMany['relation'];
            $model = is_string($hasMany) ? $hasMany : $hasMany['model'];
            $foreignKey = (is_string($hasMany) ? null : array_key_exists('foreignKey', $hasMany)) ? $hasMany['foreignKey'] : null;
            $relationship = $this->getRelationshipFunction($field, 'hasMany', $hasManys, $model, $foreignKey);
            $relationships[$field] = $relationship;
        }
        // $stub = str_replace(['{{ hasMany }}', '{{hasMany}}'], $hasManys, $stub);

        //hasOneThrough
        $hasOneThroughs = '';
        foreach ($jsonData['hasOneThrough'] as $hasOneThrough) {
            $field = is_string($hasOneThrough) ? $hasOneThrough : $hasOneThrough['relation'];
            $model = is_string($hasOneThrough) ? $hasOneThrough : $hasOneThrough['model'];
            $foreignKey = (is_string($hasOneThrough) ? null : array_key_exists('foreignKey', $hasOneThrough)) ? $hasOneThrough['foreignKey'] : null;
            $relationship = $this->getRelationshipFunction($field, 'hasOneThrough', $hasOneThroughs, $model, $foreignKey);
            $relationships[$field] = $relationship;
        }
        // $stub = str_replace(['{{ hasOneThrough }}', '{{hasOneThrough}}'], $hasOneThroughs, $stub);

        //hasManyThrough
        $hasManyThroughs = '';
        foreach ($jsonData['hasManyThrough'] as $hasManyThrough) {
            $field = $hasManyThrough['relation'];
            $model = [$hasManyThrough['modelA'], $hasManyThrough['modelB']];
            $foreignKey = (is_string($hasManyThrough) ? null : array_key_exists('foreignKey', $hasManyThrough)) ? $hasManyThrough['foreignKey'] : null;
            $relationship = $this->getRelationshipFunction($field, 'hasManyThrough', $hasManyThroughs, $model, $foreignKey);
            $relationships[$field] = $relationship;
        }
        // $stub = str_replace(['{{ hasManyThrough }}', '{{hasManyThrough}}'], $hasManyThroughs, $stub);

        //morphMany
        $morphManys = '';
        foreach ($jsonData['morphMany'] as $index => $morphMany) {
            $relationship = $this->getRelationshipFunction($index, 'morphMany', $morphManys, $morphMany);
            $relationships[$index] = $relationship;
        }
        // $stub = str_replace(['{{ morphMany }}', '{{morphMany}}'], $morphManys, $stub);

        //morphToMany
        $morphToManys = '';
        foreach ($jsonData['morphToMany'] as $index => $morphToMany) {
            $relationship = $this->getRelationshipFunction($index, 'morphToMany', $morphToManys, $morphToMany);
            $relationships[$index] = $relationship;
        }
        // $stub = str_replace(['{{ morphToMany }}', '{{morphToMany}}'], $morphToManys, $stub);

        //morphTo
        $morphTos = '';
        foreach ($jsonData['morphTo'] as $index => $morphTo) {
            $relationship = $this->getRelationshipFunction($morphTo, 'morphTo', $morphTos, $morphTo);
            $relationships[$morphTo] = $relationship;
        }
        // $stub = str_replace(['{{ morphToMany }}', '{{morphToMany}}'], $morphToManys, $stub);

        //morphOne
        $morphOnes = '';
        foreach ($jsonData['morphOne'] as $index => $morphOne) {
            $relationship = $this->getRelationshipFunction($index, 'morphOne', $morphOnes, $morphOne);
            $relationships[$index] = $relationship;
        }
        // $stub = str_replace(['{{ morphOne }}', '{{morphOne}}'], $morphOnes, $stub);

        //morphedByMany
        $morphedByManys = '';
        foreach ($jsonData['morphedByMany'] as $index => $morphedByMany) {
            if(is_array($morphedByMany)){
                foreach($morphedByMany as $item){
                    $relationship = $this->getRelationshipFunction($index, 'morphedByMany', $morphedByManys, $item);
                    $relationships[$index  . '_' .  $item] = $relationship;
                }
            } else {
                $relationship = $this->getRelationshipFunction($index, 'morphedByMany', $morphedByManys, $morphedByMany);
                $relationships[$index] = $relationship;
            }
        }

        // $stub = str_replace(['{{ morphedByMany }}', '{{morphedByMany}}'], $morphedByManys, $stub);
// dd($relationships);
        ksort($relationships);
        $relationships = implode('', $relationships);

        $stub = str_replace(['{{ relationships }}', '{{relationships}}'], $relationships, $stub);

    }

    protected function getRelationshipFunction($field, $type, &$fullString, $model, $foreignKey = null)
    {
        $foreignKeyString = '';
        if ($foreignKey) {
            $foreignKeyString = ', \'' . $foreignKey . '\'';
        }
        $mainModel = ucwords(str_replace('-', ' ', $this->option('model')));
        if (in_array($type, ['morphMany', 'morphToMany', 'morphOne', 'morphedByMany'])) {
            $relationship = "\t" . 'public function ' . Str::camel(Str::plural($model)) . '()' . PHP_EOL . "\t" . '{' . PHP_EOL . "\t\t" . "abort(501, '" . $mainModel . ": " . Str::title(Str::plural(Str::replace('-', ' ', $model)))  . " Not implemented');" . PHP_EOL . "\t\t" . '//return $this->' . $type . '(' . Str::ucfirst(Str::camel($model)) . '::class, \'' . $field . '\');' . PHP_EOL . "\t" . '}' . PHP_EOL . PHP_EOL;
        } else if (in_array($type, ['hasOne', 'belongsTo', 'hasOneThrough'])) {
            $relationship = "\t" . 'public function ' . Str::camel($field) . '()' . PHP_EOL . "\t" . '{' . PHP_EOL . "\t\t" . "abort(501, '" . $mainModel . ": " . Str::title((Str::replace('-', ' ', $field)))  ." Not implemented');" . PHP_EOL . "\t\t" . '//return $this->' . $type . '(' . Str::ucfirst(Str::camel($model)) . '::class' . $foreignKeyString . ');' . PHP_EOL . "\t" . '}' . PHP_EOL . PHP_EOL;
        } else if ($type === 'morphTo') {
            $relationship = "\t" . 'public function ' . $field . '()' . PHP_EOL . "\t" . '{' . PHP_EOL . "\t\t" . "abort(501, '" . $mainModel . ": " . Str::title((Str::replace('-', ' ', $field)))  ." Not implemented');"  . PHP_EOL . "\t\t" . '//return $this->morphTo();' . PHP_EOL . "\t" . '}' . PHP_EOL . PHP_EOL;
        } else if ($type === 'hasManyThrough') {
            $relationship = "\t" . 'public function ' . Str::camel($field) . '()' . PHP_EOL . "\t" . '{' . PHP_EOL . "\t\t" . "abort(501, '" . $mainModel . ": " . Str::title((Str::replace('-', ' ', $field)))  ." Not implemented');" . PHP_EOL . "\t\t" . '//return $this->' . $type . '(' . Str::ucfirst(Str::camel($model[0])) . '::class, ' . Str::ucfirst(Str::camel($model[1])) . '::class);' . PHP_EOL . "\t" . '}' . PHP_EOL . PHP_EOL;
        } else {
            $relationship = "\t" . 'public function ' . Str::camel(Str::plural($field)) . '()' . PHP_EOL . "\t" . '{' . PHP_EOL . "\t\t" . "abort(501, '" . $mainModel . ": " . Str::title((Str::plural(Str::replace('-', ' ', $field))))  ." Not implemented');" . PHP_EOL . "\t\t" . '//return $this->' . $type . '(' . Str::ucfirst(Str::camel($model)) . '::class' . $foreignKeyString . ');' . PHP_EOL . "\t" . '}' . PHP_EOL . PHP_EOL;
        }
        $fullString .= $relationship;

        return $relationship;
    }

    protected function replaceNamespace(&$stub, $name): MakeModel
    {
        $name = class_basename(str_replace('\\', '/', $name));

        $stub = str_replace('{Component}', $name, $stub);

        return $this;
    }

    protected function getPath($name): string
    {
        $fileName = $this->option('path');
        return "{$this->laravel['path']}/../app/Models/$fileName";
    }
}
