<?php

namespace App\Console\Commands\Setup;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueueableAction\ActionMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeBlade extends GeneratorCommand
{
    protected $name = 'make:blade';

    protected $description = 'Create a new blade file';

    protected $type = 'Blade';


    protected function getStub(): string
    {
        $stubs = [
            'index' => $this->resolveStubPath('/stubs/blade.index.stub'),
            'show' => $this->resolveStubPath('/stubs/blade.show.stub'),
        ];

//        if (!isset($stubs[$this->option('template')])) {
//            return parent::getStub();
//        }

        return $stubs[$this->option('template')];
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options = array_merge($options, [
            ['path', null, InputOption::VALUE_REQUIRED, 'Blade file path'],
            ['model', null, InputOption::VALUE_REQUIRED, 'Blade model'],
            ['model-plural', null, InputOption::VALUE_REQUIRED, 'Blade model plural'],
            ['resource', null, InputOption::VALUE_OPTIONAL, 'Blade resource'],
            ['model-slug', null, InputOption::VALUE_OPTIONAL, 'Blade model slug'],
            ['template', null, InputOption::VALUE_OPTIONAL, 'Template: index, show'],
        ]);

        return $options;
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $stub = str_replace(['{{ modelCamel }}', '{{modelCamel}}'], Str::camel($this->option('model')), $stub);
        $stub = str_replace(['{{ modelKebab }}', '{{modelKebab}}'], Str::kebab($this->option('model')), $stub);
        $stub = str_replace(['{{ resource }}', '{{resource}}'], $this->option('resource'), $stub);
        $stub = str_replace(['{{ modelSnake }}', '{{modelSnake}}'], $this->option('model-slug'), $stub);
        $stub = str_replace(['{{ modelPlural }}', '{{modelPlural}}'], $this->option('model-plural'), $stub);
        $stub = str_replace(['{{ modelPluralCapitalise }}', '{{modelPluralCapitalise}}'], ucwords($this->option('model-plural')), $stub);
        $stub = str_replace(['{{ modelCapitalise }}', '{{modelCapitalise}}'], ucwords($this->option('model')), $stub);
        $stub = str_replace(['{{ modelUpper }}', '{{modelUpper}}'], strtoupper($this->option('model')), $stub);
        $stub = str_replace(['{{ model }}', '{{model}}'], strtolower($this->option('model')), $stub);
        return $stub;
    }

    /**
     * Determine if the class already exists.
     *
     * @param string $rawName
     * @return bool
     */
    protected function alreadyExists($rawName): bool
    {
        $name = class_basename(str_replace('\\', '/', $rawName));
        $path = $this->option('path');
        $path = "{$this->laravel['path']}/../resources/views/$path";

        return file_exists($path);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $name = class_basename(str_replace('\\', '/', $name));

        $stub = str_replace('{Component}', $name, $stub);

        return $this;
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = class_basename(str_replace('\\', '/', $name));
        $path = $this->option('path');
        return "{$this->laravel['path']}/../resources/views/$path";
    }
}
