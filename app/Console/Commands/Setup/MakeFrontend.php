<?php

namespace App\Console\Commands\Setup;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeFrontend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:frontend {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate basic files for frontend, please use singular name';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $modelName = ucfirst(Str::camel($this->argument('model'))); //replace with argument

        $modelPlural = strtolower(Str::plural($modelName));
        $modelPluralCapitalised = ucwords(Str::plural($modelName));

        $types = ['vue-index', 'vue-create', 'blade-show', 'blade-index'];

        $resource = $modelName . 'Resource';
        $modelSlug = Str::slug(Str::snake($modelName));

        Artisan::call("make:vue Senses/{$modelPluralCapitalised}/{$modelName}Table.vue --path=Senses/{$modelPluralCapitalised}/{$modelName}Table.vue --template=index --model={$modelName} --resource={$resource} --model-slug={$modelSlug} --model-plural={$modelPlural}");
        Artisan::call("make:vue Senses/{$modelPluralCapitalised}/{$modelName}Form.vue --path=Senses/{$modelPluralCapitalised}/{$modelName}Form.vue --template=form --model={$modelName} --resource={$resource} --model-slug={$modelSlug} --model-plural={$modelPlural}");

        Artisan::call("make:blade {$modelPlural}/show.blade.php --path={$modelPlural}/show.blade.php --template=show --model={$modelName} --resource={$resource} --model-slug={$modelSlug} --model-plural={$modelPlural}");
        Artisan::call("make:blade {$modelPlural}/index.blade.php --path={$modelPlural}/index.blade.php --template=index --model={$modelName} --resource={$resource} --model-slug={$modelSlug} --model-plural={$modelPlural}");


        $this->info('Generated ' . implode(', ', $types) . ' successfully');
    }
}
