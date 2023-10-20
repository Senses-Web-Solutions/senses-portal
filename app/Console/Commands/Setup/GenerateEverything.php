<?php

namespace App\Console\Commands\Setup;

use App\Actions\Abilities\GenerateAbilities;
use App\Actions\Roles\AssignRoleToUser;
use App\Actions\Roles\GenerateSensesRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateEverything extends Command
{
    protected $signature = 'generate:everything {--force}';
    protected $description = 'Run the generation command for all models in the array below';
    protected $models = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('---------- Generation start ----------');
        $this->newLine();
        $force = $this->option('force');
        $files = scandir('json_files');
        $bar = $this->output->createProgressBar(count($files));
        foreach ($files as $file) {
            if (str_contains($file, '.json') && $file !== '_template.json' && $file !== 'test.json' && $file !== 'pineapple.json' && $file !== 'json-template.json') {
                $model = substr($file, 0, -5);
                $this->call("make:everything", ["model" => "{$model}", "--force" => $force, "type" => "all"]);
                $bar->advance();
            }
        }
        $bar->finish();

        $this->newLine();

        if ($force || $this->confirm('Do you want to migrate the database? If you did not delete duplicate migrations, this will throw errors.')) {
            $this->line('Migrating database');
            Artisan::call("migrate:fresh --seed");
            $this->info('Database migrated');
        }

//        app(GenerateAbilities::class)->execute();
//        $role = app(GenerateSensesRole::class)->execute();
//        app(AssignRoleToUser::class)->execute($role, User::find(1));
//        app(AssignRoleToUser::class)->execute($role, User::find(2));
//        app(AssignRoleToUser::class)->execute($role, User::find(3));
//        app(AssignRoleToUser::class)->execute($role, User::find(4));
//        app(AssignRoleToUser::class)->execute($role, User::find(5));
//        app(AssignRoleToUser::class)->execute($role, User::find(6));
//        app(AssignRoleToUser::class)->execute($role, User::find(7));
//        app(AssignRoleToUser::class)->execute($role, User::find(8));

        if ($this->confirm('Do you want to build?')) {
            $this->line('Running yarn dev');
            exec('yarn dev');
            $this->newLine();
            $this->info('---------- Generation finished ----------');
        }
    }
}
