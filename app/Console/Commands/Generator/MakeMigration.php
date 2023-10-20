<?php

namespace App\Console\Commands\Generator;

use Carbon\Carbon;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueueableAction\ActionMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeMigration extends GeneratorCommand
{
    protected $name = 'make:senses_migration';

    protected $description = 'Create a new migration';

    protected $type = 'Migration';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/generator/migration.stub');
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

        $fields = '';

        foreach ($jsonData['fields'] as $field) {
            if ($field['type'] === 'morphs') {
                if (array_key_exists('nullable', $field) && $field['nullable']) {
                    $fieldString = '$table->nullableMorphs(\'' . $field['name'] . '\')';
                } else {
                    $fieldString = '$table->morphs(\'' . $field['name'] . '\')';
                }
            } else if ($field['type'] === 'colour') {
                $fieldString = '$table->' . $field['type'] . '()';
            } else if ($field['type'] === 'geom') {
                $fieldString = '$table->' . $field['type'] . '(\'' . $field['name'] . '\')';
            } else {
                $params = null;
                if (array_key_exists('params', $field) && isset($field['params']) && count($field['params'])) {
                    $params = ', ' . implode(', ', $field['params']);
                }
                if ($field['type'] === 'relationship') {
                    $fieldString = '$table->unsignedBigInteger(\'' . $field['name'] . '\'' . $params . ')';
                } else {
                    $fieldString = '$table->' . $field['type'] . '(\'' . $field['name'] . '\'' . $params . ')';
                }
                if (array_key_exists('index', $field) && $field['index']) {
                    $fieldString .= '->index()';
                }
                if (array_key_exists('nullable', $field) && $field['nullable']) {
                    $fieldString .= '->nullable()';
                }
                if (array_key_exists('unique', $field) && $field['unique']) {
                    $fieldString .= '->unique()';
                }
                if (array_key_exists('default', $field) && isset($field['default'])) {
                    $fieldString .= '->default(' . $field['default'] . ')';
                }
            }
            $fieldString .= ';' . PHP_EOL . "\t\t\t";
            $fields .= $fieldString;
        }

        $stub = str_replace(['{{ fields }}', '{{fields}}'], $fields, $stub); //Assignment Groups

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
        return "{$this->laravel['path']}/../database/migrations/$fileName";
    }
}
