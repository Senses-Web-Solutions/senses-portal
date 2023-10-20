<?php

namespace App\Console\Commands\Setup;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate basic files for api, please use singular name';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $modelName = ucfirst(Str::camel($this->argument('model'))); //replace with argument

        $modelPluralName = Str::plural($modelName);

        $types = ['model', 'migration', 'factory', 'api controller', 'observer', 'resource', 'test', 'requests', 'actions'];

        $resource = $modelName . 'Resource';
        $modelSlug = Str::slug(Str::snake($modelName));

        Artisan::call("make:model {$modelName} -m -f");

        Artisan::call("make:controller Api/{$modelName}Controller --api --model={$modelName}");

        Artisan::call("make:observer {$modelName}Observer --model={$modelName}");

        Artisan::call("make:resource {$modelName}Resource");

        Artisan::call("make:test {$modelName}Test");

        Artisan::call("make:request {$modelPluralName}/Create{$modelName}Request");
        Artisan::call("make:request {$modelPluralName}/Update{$modelName}Request");
        Artisan::call("make:request {$modelPluralName}/List{$modelName}Request");
        Artisan::call("make:request {$modelPluralName}/Show{$modelName}Request");
        Artisan::call("make:request {$modelPluralName}/Delete{$modelName}Request");

        Artisan::call("make:api_action {$modelPluralName}/Create{$modelName} --template=create --model={$modelName} --resource={$resource} --model-slug={$modelSlug}");
        Artisan::call("make:api_action {$modelPluralName}/Update{$modelName} --template=update --model={$modelName} --resource={$resource} --model-slug={$modelSlug}");
        Artisan::call("make:api_action {$modelPluralName}/Delete{$modelName} --template=delete --model={$modelName} --resource={$resource} --model-slug={$modelSlug}");
        Artisan::call("make:api_action {$modelPluralName}/Generate{$modelName}ShowCache --template=cache --model={$modelName} --resource={$resource} --model-slug={$modelSlug}");
       

        $this->info('Generated ' . implode(', ', $types) . ' successfully');
    }
}
