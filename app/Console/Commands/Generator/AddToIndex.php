<?php

namespace App\Console\Commands\Generator;

use Illuminate\Console\Command;
use Illuminate\Support\Str;


class AddToIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'senses:index {component} {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a component to the index file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $component = $this->argument('component');
        $model = $this->argument('model');

        $ucCamelModel = ucfirst(Str::camel($model));
        $ucCamelPluralModel = ucfirst(Str::plural(Str::camel($model)));

        $content = file_get_contents("resources" . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "Components" . DIRECTORY_SEPARATOR . "index.js");

        $content = str_replace('// ----- GENERATOR A -----', 'import ' . $component .' from \'./Generator/' . $ucCamelPluralModel . '/' . $component . '.vue\';' . PHP_EOL . "// ----- GENERATOR A -----", $content);
        $content = str_replace('// ----- GENERATOR B -----', "\t" . $component .',' . PHP_EOL . "// ----- GENERATOR B -----", $content);

        file_put_contents("resources" . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "Components" . DIRECTORY_SEPARATOR . "index.js", $content);
    }
}
