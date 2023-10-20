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

class MakeRelationshipTableVue extends GeneratorCommand
{
    protected $name = 'make:senses_relationship_table_vue';

    protected $description = 'Create a new relationship table vue file';

    protected $type = 'Form Vue';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/generator/relationship-table-vue.stub');
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        return array_merge($options, [
            ['model', null, InputOption::VALUE_REQUIRED, 'Model'],
            ['relModel', null, InputOption::VALUE_REQUIRED, 'Relationship Model'],
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

        $stub = str_replace(['{{ pluralModel }}', '{{pluralModel}}'], Str::plural($this->option('model')), $stub); //assignment-groups
        $stub = str_replace(['{{ title }}', '{{title}}'],  ucwords(str_replace('-', ' ', $this->option('relModel'))). ' ' . ucwords(str_replace('-', ' ', Str::plural($this->option('model')))), $stub); // Company Assignment Groups
        $stub = str_replace(['{{ generationDate }}', '{{generationDate}}'], Carbon::now()->format('d-m-Y H:i:s'), $stub);

        return $stub;
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
