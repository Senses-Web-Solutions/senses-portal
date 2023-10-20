<?php

namespace App\Console\Commands\Setup;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueueableAction\ActionMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeVue extends GeneratorCommand
{
    protected $name = 'make:vue';

    protected $description = 'Create a new vue file';

    protected $type = 'Vue';

    protected function getStub(): string
    {
        $stubs = [
            'index' => $this->resolveStubPath('/stubs/vue.index.stub'),
            'form' => $this->resolveStubPath('/stubs/vue.form.stub'),
        ];

        return $stubs[$this->option('template')];
    }

    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options = array_merge($options, [
            ['path', null, InputOption::VALUE_REQUIRED, 'Vue file path'],
            ['model', null, InputOption::VALUE_REQUIRED, 'Vue model'],
            ['model-plural', null, InputOption::VALUE_REQUIRED, 'Vue model plural'],
            ['resource', null, InputOption::VALUE_OPTIONAL, 'Vue resource'],
            ['model-slug', null, InputOption::VALUE_OPTIONAL, 'Vue model slug'],
            ['template', null, InputOption::VALUE_OPTIONAL, 'Template: index, form'],
        ]);

        return $options;
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                        ? $customPath
                        : __DIR__.$stub;
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $stub = str_replace(['{{ modelCamel }}', '{{modelCamel}}'], Str::camel($this->option('model')), $stub);
        $stub = str_replace(['{{ modelKebab }}', '{{modelKebab}}'], Str::kebab($this->option('model')), $stub);
        $stub = str_replace(['{{ resource }}', '{{resource}}'], $this->option('resource'), $stub);
        $stub = str_replace(['{{ modelSnake }}', '{{modelSnake}}'], $this->option('model-slug'), $stub);
        $stub = str_replace(['{{ modelPlural }}', '{{modelPlural}}'], $this->option('model-plural'), $stub);
        $stub = str_replace(['{{ modelCapitalise }}', '{{modelCapitalise}}'], ucwords($this->option('model')), $stub);
        $stub = str_replace(['{{ modelUpper }}', '{{modelUpper}}'], strtoupper($this->option('model')), $stub);
        $stub = str_replace(['{{ model }}', '{{model}}'], strtolower($this->option('model')), $stub);
        return $stub;
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName): bool
    {
        $name = class_basename(str_replace('\\', '/', $rawName));
        $path = $this->option('path');
        $path = "{$this->laravel['path']}/../app/Http/Controllers/Api/$path";

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
    protected function getPath($name): string
    {
        $name = class_basename(str_replace('\\', '/', $name));
        $path = $this->option('path');
        return "{$this->laravel['path']}/../app/Http/Controllers/Api/$path";
    }
}
