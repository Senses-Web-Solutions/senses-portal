<?php

namespace App\Console\Commands\Generator;

use Carbon\Carbon;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueueableAction\ActionMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeApiController extends GeneratorCommand
{
    protected $name = 'make:senses_api_controller';

    protected $description = 'Create a new API controller';

    protected $type = 'API Controller';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/generator/api-controller.stub');
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

    protected function resolveStubPath($stub)
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
        $stub = str_replace(['{{ ucCamelModel }}', '{{ucCamelModel}}'], ucfirst(Str::camel($this->option('model'))), $stub); //AssignmentGroup
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

        //hasOne
        $hasOnes = '';
        foreach ($jsonData['hasOne'] as $hasOne) {
            $field = is_string($hasOne) ? $hasOne : $hasOne['relation'];
            $this->getRelationshipFunction($field, 'hasOne', $hasOnes);
        }
        $stub = str_replace(['{{ hasOne }}', '{{hasOne}}'], $hasOnes, $stub);

        //belongsTo
        $belongsTos = '';
        foreach ($jsonData['belongsTo'] as $belongsTo) {
            $field = is_string($belongsTo) ? $belongsTo : $belongsTo['relation'];
            $this->getRelationshipFunction($field, 'belongsTo', $belongsTos);
        }
        $stub = str_replace(['{{ belongsTo }}', '{{belongsTo}}'], $belongsTos, $stub);

        //belongsToMany
        $belongsToManys = '';
        foreach ($jsonData['belongsToMany'] as $belongsToMany) {
            $field = is_string($belongsToMany) ? $belongsToMany : $belongsToMany['relation'];
            $this->getRelationshipFunction($field, 'belongsToMany', $belongsToManys);
        }
        $stub = str_replace(['{{ belongsTo }}', '{{belongsTo}}'], $belongsTos, $stub);

        //hasMany
        $hasManys = '';
        foreach ($jsonData['hasMany'] as $hasMany) {
            $field = is_string($hasMany) ? $hasMany : $hasMany['relation'];
            $this->getRelationshipFunction($field, 'hasMany', $hasManys);
        }
        $stub = str_replace(['{{ hasMany }}', '{{hasMany}}'], $hasManys, $stub);

        //hasOneThrough
        $hasOneThroughs = '';
        foreach ($jsonData['hasOneThrough'] as $hasOneThrough) {
            $field = is_string($hasOneThrough) ? $hasOneThrough : $hasOneThrough['relation'];
            $this->getRelationshipFunction($field, 'hasOneThrough', $hasOneThroughs);
        }
        $stub = str_replace(['{{ hasOneThrough }}', '{{hasOneThrough}}'], $hasOneThroughs, $stub);

        //hasManyThrough
        $hasManyThroughs = '';
        foreach ($jsonData['hasManyThrough'] as $hasManyThrough) {
            $field = is_string($hasManyThrough) ? $hasManyThrough : $hasManyThrough['relation'];
            $this->getRelationshipFunction($field, 'hasManyThrough', $hasManyThroughs);
        }
        $stub = str_replace(['{{ hasManyThrough }}', '{{hasManyThrough}}'], $hasManyThroughs, $stub);

        //morphMany
        $morphManys = '';
        foreach ($jsonData['morphMany'] as $index => $morphMany) {
            $this->getRelationshipFunction([$index, $morphMany], 'morphMany', $morphManys);
        }
        $stub = str_replace(['{{ morphMany }}', '{{morphMany}}'], $morphManys, $stub);

        //morphToMany
        $morphToManys = '';
        foreach ($jsonData['morphToMany'] as $index => $morphToMany) {
            $this->getRelationshipFunction([$index, $morphToMany], 'morphToMany', $morphToManys);
        }
        $stub = str_replace(['{{ morphToMany }}', '{{morphToMany}}'], $morphToManys, $stub);

        //morphOne
        $morphOnes = '';
        foreach ($jsonData['morphOne'] as $index => $morphOne) {
            $this->getRelationshipFunction([$index, $morphOne], 'morphOne', $morphOnes);
        }
        $stub = str_replace(['{{ morphOne }}', '{{morphOne}}'], $morphOnes, $stub);

        //morphedByMany
        $morphedByManys = '';
        foreach ($jsonData['morphedByMany'] as $index => $morphedByMany) {
            if(is_array($morphedByMany)){
                foreach($morphedByMany as $item){
                    $this->getRelationshipFunction([$index, $item], 'morphedByMany', $morphedByManys);
                }
            } else {
                $this->getRelationshipFunction([$index, $morphedByMany], 'morphedByMany', $morphedByManys);
            }
        }
        $stub = str_replace(['{{ morphedByMany }}', '{{morphedByMany}}'], $morphedByManys, $stub);

//        $traits = '';
//        foreach($jsonData['traits'] as $trait){
//            //todo is this needed??? - maybe for tags and status but not others??
//        }
//        $stub = str_replace(['{{ traits }}', '{{traits}}'], $traits, $stub);

    }

    protected function getRelationshipFunction($field, $type, &$fullString)
    {
        $variables = [
            'myString' => Str::camel($this->option('model')),
            'my-string' => Str::kebab($this->option('model')),
            'myStrings' => Str::plural(Str::camel($this->option('model'))),
            'MyString' => ucfirst(Str::camel($this->option('model'))),
            'MyStrings' => ucfirst(Str::plural(Str::camel($this->option('model')))),
            'My String' => Str::title(Str::replace('-', ' ', $this->option('model'))),
            'My Strings' => Str::title(Str::plural(Str::replace('-', ' ', $this->option('model')))),
            'my strings' => strtolower(Str::plural(Str::replace('-', ' ', $this->option('model')))),
            'my string' => strtolower(Str::replace('-', ' ', $this->option('model'))),
        ];

        if (in_array($type, ['hasOne', 'belongsTo', 'hasOneThrough'])) {
            $string = "\t" . '/**' . PHP_EOL . "\t" . '* ' . Str::camel($field) . $variables['MyStrings'] . '()' . PHP_EOL . "\t" . '*' . PHP_EOL . "\t" . '* Lists ' . $variables['my strings'] . ' based on their ' . Str::replace('-', ' ', $field) . '.' . PHP_EOL . "\t" . '* <aside><ul><li>list-' . $variables['my-string'] . '</li></ul></aside>' . PHP_EOL . "\t" . '* @urlParam ' . Str::kebab(Str::plural($field)) . ' integer[] ' . Str::title(Str::replace('-', ' ', $field)) . ' IDs Example: [1,2,3]' . PHP_EOL . "\t" . '*/';
            $string .= PHP_EOL . "\t" . 'public function ' . Str::camel($field) . $variables['MyStrings'] . '(string $' . Str::camel($field) . 'IDs)' . PHP_EOL . "\t" . '{'. PHP_EOL . "\t\t" . '$' . Str::camel($field) . 'IDs = explode(\',\', $' . Str::camel($field) .'IDs);' . PHP_EOL . "\t\t" . 'abort(501, \'' . ucfirst(Str::replace('-', ' ', $field)) . ' ' . $variables['my strings'] . ' not implemented\');' . PHP_EOL . "\t\t" . '//return $this->respond(QueryBuilder::for(' . $variables['MyString'] . '::class)->whereIn(\'' . str_replace('-', '_', $field) . '_id\', $' . Str::camel($field) . 'IDs)->list());' . PHP_EOL . "\t" . '}' . PHP_EOL;
        } else if (in_array($type, ['belongsToMany', 'hasMany', 'hasManyThrough'])) {
            $string = "\t" . '/**' . PHP_EOL . "\t" . '* ' . Str::camel($field) . $variables['MyStrings'] . '()' . PHP_EOL . "\t" . '*' . PHP_EOL . "\t" . '* Lists ' . $variables['my strings'] . ' based on their ' . Str::replace('-', ' ', $field) . '.' . PHP_EOL . "\t" . '* <aside><ul><li>list-' . $variables['my-string'] . '</li></ul></aside>' . PHP_EOL . "\t" . '* @urlParam ' . Str::kebab(Str::plural($field)) . ' integer[] ' . Str::title(Str::replace('-', ' ', $field)) . ' IDs Example: [1,2,3]' . PHP_EOL . "\t" . '*/';
            $string .= PHP_EOL . "\t" . 'public function ' . Str::camel($field) . $variables['MyStrings'] . '(string $' . Str::camel($field) . 'IDs)' . PHP_EOL . "\t" . '{' . PHP_EOL . "\t\t" . '$' . Str::camel($field) . 'IDs = explode(\',\', $' . Str::camel($field) .'IDs);' . PHP_EOL . "\t\t" . 'abort(501, \'' . ucfirst(Str::replace('-', ' ', $field)) . ' ' . $variables['my strings'] . ' not implemented\');' . PHP_EOL . "\t\t" . '//return $this->respond(QueryBuilder::for(' . $variables['MyString'] . '::class)->whereHas(\'' . Str::plural(Str::camel($field)) . '\', function ($q) use ($' . Str::camel($field) . 'IDs) {$q->whereIn(\'id\', $' . Str::camel($field) . 'IDs);})->list());' . PHP_EOL . "\t" . '}' . PHP_EOL;
        } else {
            $string = "\t" . '/**' . PHP_EOL . "\t" . '* ' . Str::camel($field[1]) . $variables['MyStrings'] . '()' . PHP_EOL . "\t" . '*' . PHP_EOL . "\t" . '* Lists ' . $variables['my strings'] . ' based on their ' . Str::replace('-', ' ', $field[1]) . '.' . PHP_EOL . "\t" . '* <aside><ul><li>list-' . $variables['my-string'] . '</li></ul></aside>' . PHP_EOL . "\t" . '* @urlParam ' . Str::kebab(Str::plural($field[1])) . ' integer[] ' . Str::title(Str::replace('-', ' ', $field[1])) . ' IDs Example: [1,2,3]' . PHP_EOL . "\t" . '*/';
            $string .= PHP_EOL . "\t" . 'public function ' . Str::camel($field[1]) . $variables['MyStrings'] . '(string $' . Str::camel($field[1]) . 'IDs)' . PHP_EOL . "\t" . '{' . PHP_EOL . "\t\t" . '$' . Str::camel($field[1]) . 'IDs = explode(\',\', $' . Str::camel($field[1]) .'IDs);' .  PHP_EOL . "\t\t" . 'abort(501, \'' . ucfirst(Str::replace('-', ' ', $field[1])) . ' ' . $variables['my strings'] . ' not implemented\');' . PHP_EOL . "\t\t" . '//return $this->respond(QueryBuilder::for(' . $variables['MyString'] . '::class)->whereHas(\'' . Str::plural(Str::camel($field[1])) .'\', function ($q) use ($' . Str::camel($field[1]) . 'IDs) {$q->whereIn(\'' . $field[0] .'_id\', $' . Str::camel($field[1]) .'IDs); $q->where(\'' . $field[0] .'_type\', \'' . $field[1] . '\');})->list());' . PHP_EOL . "\t" . '}' . PHP_EOL;
        }

        $fullString .= $string . PHP_EOL;
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $name = class_basename(str_replace('\\', '/', $name));

        $stub = str_replace('{Component}', $name, $stub);

        return $this;
    }

    protected function getPath($name): string
    {
        $fileName = $this->option('path');
        return "{$this->laravel['path']}/../app/Http/Controllers/Api/$fileName";
    }
}
