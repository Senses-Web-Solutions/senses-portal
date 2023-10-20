<?php

namespace App\Console\Commands\Generator;

use Carbon\Carbon;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueueableAction\ActionMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakePivotMigration extends GeneratorCommand
{
    protected $name = 'make:senses_pivot_migration';

    protected $description = 'Create a new pivot migration';

    protected $type = 'Pivot Migration';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/generator/pivot-migration.stub');
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        return array_merge($options, [
            ['model', null, InputOption::VALUE_REQUIRED, 'Model'],
            ['second_model', null, InputOption::VALUE_REQUIRED, 'Second Model'],
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
        $stub = str_replace(['{{ camelModel }}', '{{camelModel}}'], Str::camel($this->option('model')), $stub); //assignmentGroup
        $stub = str_replace(['{{ ucCamelModel }}', '{{ucModel}}'], ucfirst(Str::camel($this->option('model'))), $stub); //AssignmentGroup
        $stub = str_replace(['{{ snakeModel }}', '{{snakeModel}}'], str_replace('-', '_', $this->option('model')), $stub); //assignment_group
        $stub = str_replace(['{{ secondModel }}', '{{secondModel}}'], $this->option('second_model'), $stub); //user
        $stub = str_replace(['{{ camelSecondModel }}', '{{camelSecondModel}}'], Str::camel($this->option('second_model')), $stub); //user
        $stub = str_replace(['{{ ucCamelSecondModel }}', '{{ucCamelSecondModel}}'], ucfirst(Str::camel($this->option('second_model'))), $stub); //User
        $stub = str_replace(['{{ snakeSecondModel }}', '{{snakeSecondModel}}'], str_replace('-', '_', $this->option('second_model')), $stub); //user

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
        return "{$this->laravel['path']}/../database/migrations/$fileName";
    }
}
