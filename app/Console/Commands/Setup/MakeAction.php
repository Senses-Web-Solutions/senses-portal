<?php

namespace App\Console\Commands\Setup;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueueableAction\ActionMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeAction extends ActionMakeCommand
{
    protected $name = 'make:api_action';

    protected $description = 'Create a new api action class';

    protected $type = 'Action';

    protected function getStub(): string
    {
        $stubs = [
            'create' => $this->resolveStubPath('/stubs/action.create.stub'),
            'update' => $this->resolveStubPath('/stubs/action.update.stub'),
            'delete' => $this->resolveStubPath('/stubs/action.delete.stub'),
            'cache' =>  $this->resolveStubPath('/stubs/action.cache.stub'),
        ];

        if (!isset($stubs[$this->option('template')])) {
            return parent::getStub();
        }

        return $stubs[$this->option('template')];
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options = array_merge($options, [
            ['model', null, InputOption::VALUE_REQUIRED, 'Action model'],
            ['resource', null, InputOption::VALUE_OPTIONAL, 'Action resource'],
            ['model-slug', null, InputOption::VALUE_OPTIONAL, 'Action model slug'],
            ['template', null, InputOption::VALUE_OPTIONAL, 'Template: create, update, delete, cache'],
        ]);

        return $options;
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                        ? $customPath
                        : __DIR__.$stub;
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $stub = str_replace(['actionModelVariable', '{{ actionModelVariable }}', '{{actionModelVariable}}'], Str::camel($this->option('model')), $stub);
        $stub = str_replace(['actionResource', '{{ actionResource }}', '{{actionResource}}'], $this->option('resource'), $stub);
        $stub = str_replace(['actionModelSlug', '{{ actionModelSlug }}', '{{actionModelSlug}}'], $this->option('model-slug'), $stub);
        $stub = str_replace(['actionModel', '{{ actionModel }}', '{{actionModel}}'], $this->option('model'), $stub);
        return $stub;
    }
}
