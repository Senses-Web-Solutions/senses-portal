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

class MakeTableVue extends GeneratorCommand
{
    protected $name = 'make:senses_table_vue';

    protected $description = 'Create a new table vue file';

    protected $type = 'Table Vue';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/generator/table-vue.stub');
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

        $fields = PHP_EOL;

        foreach ($jsonData['fields'] as $field) {

            if ((str_ends_with($field['name'], '_id') || str_ends_with($field['name'], '_by')) && $field['type'] === 'relationship') {
                if (str_ends_with($field['name'], '_id')) {
                    $model = substr($field['name'], 0, -3);
                } else {
                    $model = $field['name'];
                }
                $tableField = 'title';
                if(str_ends_with($field['name'], '_by') && $field['type'] === 'relationship'){
                    $tableField = 'full_name';
                }
                if (in_array(Str::camel($model), $jsonData['allowedEmbeds']) && in_array($model, $jsonData['allowedFields'])) {
                    $string = "\t\t\t" . '{ label: "' . Str::title($model) . '", key: "' . Str::camel($model) . '.' . $tableField . '" },';
                    $fields .= $string . PHP_EOL;
                }
            } else {
                if (in_array($field['name'], $jsonData['allowedFields'])) {
                    $string = "\t\t\t\t" . '{ label: "' . str_replace('_', ' ',Str::title($field['name'])) . '", key: "' . $field['name'] . '"';
                    if ($field['type'] === 'datetime' || $field['type'] === 'date') {
                        $string .= ', format: "datetime", filter: { type: "datetime" }';
                    } else if ($field['type'] === 'integer') {
                        $string .= ', filter: { type: "integer" }';
                    } else if ($field['type'] === 'money'){
                        $string .= ', format: "money"';
                    }
                    $fields .= $string . ' },' . PHP_EOL;
                }
            }

        }

        $stub = str_replace(['{{ fields }}', '{{fields}}'], $fields, $stub);
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
        return "{$this->laravel['path']}/../resources/js/Components/Senses/$fileName";
    }
}
