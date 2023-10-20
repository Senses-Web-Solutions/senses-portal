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

class MakeFormVue extends GeneratorCommand
{
    protected $name = 'make:senses_form_vue';

    protected $description = 'Create a new form vue file';

    protected $type = 'Form Vue';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/generator/form-vue.stub');
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

        $dataModelFields = PHP_EOL;
        $dataFields = "\t\t\t\t" . '{' . PHP_EOL . "\t\t\t\t\t" . 'title: "' . ucwords(str_replace('-', ' ', $this->option('model'))) . ' Information",' . PHP_EOL . "\t\t\t\t\t" . 'description: "Basic information about the ' . str_replace('-', ' ', $this->option('model')) . '",' . PHP_EOL . "\t\t\t\t\t" . 'fields: [' . PHP_EOL;

        foreach ($jsonData['fields'] as $field) {
            $type = $this->findFormFieldType($field['type']);
            if ($type !== 'morphs') {
                if ($field['type'] === 'boolean') {
                    $dataModelFields .= "\t\t\t\t" . $field['name'] . ': ' . 'false,' . PHP_EOL;
                } else if ($field['type'] === 'colour') {
                    $dataModelFields .= "\t\t\t\t" . $field['name'] . ': ' . '\'purple\',' . PHP_EOL;
                } else if ($field['type'] === 'date') {
                    $dataModelFields .= "\t\t\t\t" . $field['name'] . ': ' . '\'' . Carbon::now()->format('Y-m-d') . '\',' . PHP_EOL;
                } else if ($field['type'] === 'dateTime') {
                    $dataModelFields .= "\t\t\t\t" . $field['name'] . ': ' . '\'' . Carbon::now()->format('Y-m-d H:i:s') . '\',' . PHP_EOL;
                } else if ($field['type'] === 'double') {
                    $dataModelFields .= "\t\t\t\t" . $field['name'] . ': ' . '0.00,' . PHP_EOL;
                } else {
                    $dataModelFields .= "\t\t\t\t" . $field['name'] . ': ' . 'null,' . PHP_EOL;
                }
                if (array_key_exists('options', $field) && $field['options']) {
                    $options = '[';
                    foreach ($field['options'] as $option) {
                        $options .= '{"id": ' . $option . ', "title": ' . $option . '},';
                    }
                    $dataFields .= "\t\t\t\t\t\t" . '{ key: "' . $field['name'] . '", type: "' . $type . '", options: ' . ucwords(str_replace('-', ' ', str_replace('_', ' ', $options))) . ']';
                } else if (array_key_exists('url', $field) && $field['url']) {
                    $url = $field['url'] . '?format=select-search';
                    if(array_key_exists('url_params', $field) && $field['url_params']){
                        foreach($field['url_params'] as $param){
                           $url = $url . '&' . $param;
                        }
                    }
                    $dataFields .= "\t\t\t\t\t\t" . '{ key: "' . $field['name'] . '", type: "' . $type . '", field: "id", url: "' . $url . '"';
                } else {
                    $dataFields .= "\t\t\t\t\t\t" . '{ key: "' . $field['name'] . '", type: "' . $type . '"';
                }
                $dataFields .= '},' . PHP_EOL;
            }
        }

        foreach ($jsonData['relationshipFields'] as $relationshipField) {
            $dataModelFields .= "\t\t\t\t" . $relationshipField['relationship'] . ': ' . '{},' . PHP_EOL;
            $dataModelFields .= "\t\t\t\t" . $relationshipField['relationship'] . '_id: ' . 'null,' . PHP_EOL;
            $dataFields .= "\t\t\t\t\t\t" . '{ key: "' . $relationshipField['relationship'] . '", type: "' . $relationshipField['fieldType'] . '", relationship: "single", relationshipKey: "' . $relationshipField['relationship'] . '_id",';
            if ($relationshipField['url']) {
                $dataFields .= ' url: "' . $relationshipField['url'] . '?format=select-search",';
            }
            $dataFields .= '},' . PHP_EOL;
        }


        $stub = str_replace(['{{ dataModelFields }}', '{{dataModelFields}}'], $dataModelFields, $stub);
        $stub = str_replace(['{{ dataFields }}', '{{dataFields}}'], PHP_EOL . $dataFields . "\t\t\t\t\t" . ']' . PHP_EOL . "\t\t\t\t" . '},', $stub);
    }

    protected function findFormFieldType($columnType): string
    {
        $types = [
            'string' => 'text',
            'integer' => 'number',
            'double' => 'number',
            'longText' => 'textarea',
            'unsignedBigInteger' => 'number',
            'relationship' => 'select-search',
            'boolean' => 'toggle',
            'money' => 'number',
            'colour' => 'colour',
            'dateTime' => 'date-time-picker',
            'date' => 'date-picker',
        ];

        if (isset($types[$columnType])) {
            return $types[$columnType];
        }
        return 'string';
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
