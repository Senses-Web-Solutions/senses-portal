<?php

namespace App\Console\Commands\Generator;

use Carbon\Carbon;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueueableAction\ActionMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeCreateAction extends GeneratorCommand
{
    protected $name = 'make:senses_create_action';

    protected $description = 'Create a new create action';

    protected $type = 'Create Action';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/generator/create-action.stub');
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

        $relationships = '';

        foreach ($jsonData['fields'] as $field) {
            if ($field['type'] === 'relationship') { // one to one (hasOne, belongsTo, hasOneThrough)
                $camelCase = Str::camel(substr($field['name'], 0, -3)); //assignmentGroup
                $ucCamelCase = ucfirst(Str::camel(substr($field['name'], 0, -3))); //AssignmentGroup
                $string = "\t\t" . 'if(isset($data[\'' . $field['name'] . '\'])) {' . PHP_EOL;
                $string .= "\t\t\t" . '$' . Str::camel($this->option('model')) . '->' . $camelCase . '()->associate($data[\'' . $field['name'] . '\']);' . PHP_EOL . "\t\t" . '}' . PHP_EOL;
                $relationships .= $string . PHP_EOL;
            }
        }

        $oneToMany = ['hasMany', 'hasManyThrough', 'morphOne'];
        $manyToMany = ['belongsToMany', 'morphMany', 'morphToMany', 'morphedByMany'];
        $morph = ['morphTo'];

        foreach ($jsonData['relationshipFields'] as $relationshipField) {
            if (in_array($relationshipField['relationshipType'], $manyToMany)) {
                $string = "\t\t" . 'if(isset($data[\'' . $relationshipField['field'] . 's\'])) {' . PHP_EOL;
                $string .= "\t\t\t" . '$' . Str::camel($this->option('model')) . '->' . $relationshipField['relationship'] . '()->sync($data[\'' . $relationshipField['field'] . 's\']);' . PHP_EOL . "\t\t" . '}' . PHP_EOL;
                $relationships .= $string . PHP_EOL;
            } else if (in_array($relationshipField['relationshipType'], $oneToMany)) {
                $string = "\t\t" . 'if(isset($data[\'' . $relationshipField['field'] . 's\'])) {' . PHP_EOL;
                $string .= "\t\t\t" . '$' . $relationshipField['relationship'] . ' = ' . $relationshipField['model'] . '::findIn(\'id\', $data[\'' . $relationshipField['field'] . 's\']);' . PHP_EOL;
                $string .= "\t\t\t" . '$' . Str::camel($this->option('model')) . '->' . $relationshipField['relationship'] . '()->saveMany($' . $relationshipField['relationship'] . ');' . PHP_EOL . "\t\t" .  '}' . PHP_EOL;
                $relationships .= $string . PHP_EOL;
            } else if (in_array($relationshipField['relationshipType'], $morph)) {
                $string = "\t\t" . 'if(isset($data[\'' . $relationshipField['field'] . '_id\']) && isset($data[\'' . $relationshipField['field'] .'_type) {' . PHP_EOL;
                $string .= "\t\t\t" . '$modelClass = Relation::getMorphedModel($data[\'' . $relationshipField['field'] . '_type\']);' . PHP_EOL . "\t\t\t" . '$model = $modelClass::findOrFail($data[\'' . $relationshipField['field'] . '_id\']);' . PHP_EOL . "\t\t\t" .  '$' . Str::camel($this->option('model')) . '-> ' . $relationshipField['field'] . '()->associate($model);' . PHP_EOL;
                $string .= "\t\t" . '}';
                $relationships .= $string . PHP_EOL;
            }
        }

        $stub = str_replace(['{{ relationships }}', '{{relationships}}'], $relationships, $stub);

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
        return "{$this->laravel['path']}/../app/Actions/$fileName";
    }
}
