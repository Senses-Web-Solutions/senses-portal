<?php

namespace App\Console\Commands\Generator;

use Carbon\Carbon;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueueableAction\ActionMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeFeatureTest extends GeneratorCommand
{
    protected $name = 'make:senses_feature_test';

    protected $description = 'Create a new feature test';

    protected $type = 'Test';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/generator/feature-test.stub');
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

        $attributes = PHP_EOL;
        $fields = PHP_EOL;
        $fakerCode = [
            'string' => '$this->faker->text(20),',
            'longText' => '$this->faker->text(100),',
            'integer' => '$this->faker->randomDigit,',
            'colour' => '$this->faker->randomElement(Colour::toArray()),',
            'money' => '$this->faker->randomFloat(2,1,6),',
            'boolean' => '$this->faker->boolean,',
            'unsignedBigInteger' => '$this->faker->numberBetween(1,50),',
            'relationship' => '$this->faker->numberBetween(1,10),',
            'date' => '$this->faker->date(\'Y-m-d\'),',
            'dateTime' => '$this->faker->date(\'Y-m-d H:i:s\'),',
            'timestamp' => '$this->faker->unixTime,',
            'double' => '$this->faker->randomFloat(2,1,6),',
        ];

        foreach($jsonData['fields'] as $field){
            $string = "\t\t\t" . '\''. $field['name'] . '\'' . ' => ';
            if(array_key_exists($field['type'], $fakerCode)){
                $string .= $fakerCode[$field['type']];
            } else {
                $string .= '\'null\'';
            }
            $attributes .= $string . PHP_EOL;
            $fields .= "\t\t\t\t\t" . '\'' . $field['name'] . '\',' . PHP_EOL;
        }

        $stub = str_replace(['{{ attributes }}', '{{attributes}}'], $attributes, $stub);
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
        return "{$this->laravel['path']}/../tests/Feature/$fileName";
    }
}
