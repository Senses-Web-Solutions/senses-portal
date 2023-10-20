<?php

namespace App\Console\Commands\Generator;

use Carbon\Carbon;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueueableAction\ActionMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeUpdateRequest extends GeneratorCommand
{
    protected $name = 'make:senses_update_request';

    protected $description = 'Create a new update request';

    protected $type = 'Update Request';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/generator/update-request.stub');
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

        $permissions = "\t\t" . 'if (getCurrentUser()?->can(\'update-' . $this->option('model') . '\')) {' . PHP_EOL . "\t\t\t" . 'return true;' . PHP_EOL . "\t\t" . '}';
        $stub = str_replace(['{{ permissions }}', '{{permissions}}'], $permissions, $stub);

        $rules = PHP_EOL;
        foreach ($jsonData['fields'] as $field) {
            if (in_array($field['name'], $jsonData['fillables']) || $field['type'] === 'relationship') {
                if ($field['type'] !== 'morphs') {
                    $rule = "\t\t\t" . '\'' . $field['name'] . '\' => \'';
                    $additions = [];

                    //Required
                    if (array_key_exists('nullable', $field) && $field['nullable']) {
                        array_push($additions, 'nullable');
                    } else {
                        array_push($additions, 'required');
                    }
                    //Types
                    if ($field['type'] === 'string') {
                        array_push($additions, 'string');
                        array_push($additions, 'max:255');
                    } else if ($field['type'] === 'longText') {
                        array_push($additions, 'string');
                    } else if ($field['type'] === 'integer') {
                        array_push($additions, 'integer');
                    } else if ($field['type'] === 'unsignedBigInteger') {
                        array_push($additions, 'integer');
                        array_push($additions, 'min:0');
                    } else if ($field['type'] === 'money') {
                        array_push($additions, 'numeric');
                    } else if ($field['type'] === 'relationship') {
                        array_push($additions, 'integer');
                        array_push($additions, 'exists:' . str_replace('api/v2/', '', Str::plural(str_replace('-', '_', ($field['url'])))) . ',id');
                    } else if ($field['type'] === 'boolean') {
                        array_push($additions, 'boolean');
                    }
                    if (array_key_exists('unique', $field) && $field['unique']) {
                        array_push($additions, 'Rule::unique('. Str::plural(str_replace('-', '_', $this->option('model'))) .',' . $field['name'] .')->ignore($this->input(' . $field['name'] . '), ' . $field['name'] . ')');
                    }
                    if ($field['type'] === 'colour') {
                        $rule = "\t\t\t" . '\'' . $field['name'] . '\' => [';
                        foreach ($additions as $index => $addition) {
                            $additions[$index] = '\'' . $addition . '\'';
                        }
                        $rule .= implode(', ', $additions);
                        if (count($additions)) {
                            $rule .= ', new Colour' . '],';
                        } else {
                            $rule .= 'new Colour' . '],';
                        }
                    } else {
                        $rule .= implode('|', $additions) . '\',';
                    }
                } else {
                        $rule = "\t\t\t" . '\'' . $field['name'] . '_id' . '\' => [';
                        if(!$field['nullable']){
                            $rule .= '\'required\',' . PHP_EOL;
                        }
                        $rule .= 'Rule::exists(Relation::getMorphedModel($this->' . $field['name'] . '_type), \'id\')' . PHP_EOL . '],';
                        $rule .= "\t\t\t" . '\'' . $field['name'] . '_type' . '\' => [';
                        if(!$field['nullable']){
                            $rule .= '\'required\',' . PHP_EOL;
                        }
                        $rule .= 'Rule::in(array_keys(Relation::morphMap()))' . PHP_EOL . '],';
                }

                $rules .= $rule . PHP_EOL;
            }
        }

        foreach ($jsonData['relationshipFields'] as $relationshipField) {
            $rule = "\t\t\t" . '\'' . Str::plural($relationshipField['field']) . '\' => \'nullable|array\',' . PHP_EOL;
            $rule .= "\t\t\t" . '\'' . $relationshipField['field'] . '\' => \'integer|exists:' . str_replace('api/v2/', '', Str::plural(str_replace('-', '_', ($relationshipField['url'])))) . ',id\',' . PHP_EOL;
            $rules .= $rule;
        }

        $stub = str_replace(['{{ rules }}', '{{rules}}'], $rules, $stub);
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $name = class_basename(str_replace('\\', '/', $name));

        $stub = str_replace('{Component}', $name, $stub);

        return $this;
    }

    protected function getPath($name): string
    {
        $path = $this->option('path');
        return "{$this->laravel['path']}/../app/Http/Requests/$path";
    }
}
