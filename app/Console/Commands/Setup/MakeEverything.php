<?php

namespace App\Console\Commands\Setup;

use App\Actions\Abilities\GenerateAbilities;
use App\Actions\Roles\AssignRoleToUser;
use App\Actions\Roles\GenerateSensesRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

//todo discuss & decide on way of logging (log driver etc)
class MakeEverything extends Command
{
    protected $signature = 'make:everything {model} {type?} {--force}'; //task, assignment-group, company || type = all (all models) or null (one by one) || force = true/false (overwrite files!)

    protected $description = 'Create and fill all basic files for a new model';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        //Variables
        $force = $this->option('force');
        $type = $this->argument('type');
        $model = $this->argument('model'); //assignment-group
        $pluralModel = Str::plural($this->argument('model')); //assignment-groups
        $ucCamelModel = ucfirst(Str::camel($this->argument('model'))); //AssignmentGroup
        $ucCamelPluralModel = Str::plural(ucfirst(Str::camel($this->argument('model')))); //AssignmentGroups
        $snakeModel = str_replace('-', '_', $this->argument('model')); //assignment_group
        $snakePluralModel = Str::plural(str_replace('-', '_', $this->argument('model'))); //assignment_groups
        $sentencePluralModel = ucwords(str_replace('-', ' ', Str::plural($this->argument('model')))); //Assignment Groups


        if (!file_exists("json_files" . DIRECTORY_SEPARATOR . $model . ".json")) {
            $this->info('ERROR: Json file does not exist. Create and fill a ' . $model . '.json file in the json_files folder and try again.');
        } else {
            if ($type !== 'all') {
                $this->info('---------- Generation start ----------');
                $this->newLine();
            }

            $file = file_get_contents("json_files" . DIRECTORY_SEPARATOR . $model . ".json");
            $jsonData = json_decode($file, true);

            //---------- Model ----------
            Artisan::call("make:senses_model {$model} --force=" . $force . " --model={$model} --path={$ucCamelModel}.php");
            if ($type !== 'all') {
                $this->info('Generated model successfully');
            }

            //---------- Migration ----------
            $now = Carbon::now();
            $dateTime = $now->format('Y_m_d_His');
            $files = scandir('database' . DIRECTORY_SEPARATOR . 'migrations');
            $name = "_create_" . $snakePluralModel . "_table.php";
            $results = array_filter($files, function ($file) use ($name) {
                return strpos($file, $name) !== false;
            });
            $delete = false;
            if ($force) {
                $delete = true;
            } else if (count($results) > 0) {
                $this->warn('Migrations already exist for this model.');
                if ($this->confirm('Do you want to delete the other migrations before you create the new one?')) {
                    $delete = true;
                } else {
                    $this->warn('Migrating the database will throw errors.');
                }
            }
            if ($delete) {
                foreach ($results as $result) {
                    unlink('database'  . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . $result);
                }
                if ($type !== 'all') {
                    $this->info('Migrations deleted.');
                }
            }
            Artisan::call("make:senses_migration {$model} --force=" . $force . " --model={$model} --path={$dateTime}_create_{$snakePluralModel}_table.php");
            if ($type !== 'all') {
                $this->info('Generated migration successfully');
            }

            //---------- Controllers ----------
            Artisan::call("make:senses_api_controller {$model} --force=" . $force . " --model={$model} --path={$ucCamelModel}Controller.php");
            if ($jsonData['frontend']) {
                Artisan::call("make:senses_web_controller {$model} --force=" . $force . " --model={$model} --path={$ucCamelModel}Controller.php");
            }
            if ($type !== 'all') {
                $this->info('Generated API and Web controllers successfully');
            }

            //---------- Routes ----------
            $apiContent = file_get_contents("routes" . DIRECTORY_SEPARATOR . "api.php");
            if (!str_contains($apiContent, "use App\Http\Controllers\Api\\" . $ucCamelModel . "Controller;")) {
                $apiContent = str_replace('// ----- GENERATOR 1 -----', "use App\Http\Controllers\Api\\" . $ucCamelModel . "Controller;" . PHP_EOL . "// ----- GENERATOR 1 -----", $apiContent);
            }
            if (!str_contains($apiContent, "'" . $pluralModel . "' => " . $ucCamelModel . "Controller::class")) {
                $apiContent = str_replace('// ----- GENERATOR 2 -----', "'" . $pluralModel . "' => " . $ucCamelModel . "Controller::class," . PHP_EOL . "\t\t// ----- GENERATOR 2 -----", $apiContent);
            }
            file_put_contents("routes" . DIRECTORY_SEPARATOR . "api.php", $apiContent);
            if ($jsonData['frontend']) {
                $webContent = file_get_contents("routes" . DIRECTORY_SEPARATOR . "web.php");
                if (!str_contains($webContent, "use App\Http\Controllers\Web\\" . $ucCamelModel . "Controller;")) {
                    $webContent = str_replace('// ----- GENERATOR 1 -----', "use App\Http\Controllers\Web\\" . $ucCamelModel . "Controller;" . PHP_EOL . "// ----- GENERATOR 1 -----", $webContent);
                }
                if (!str_contains($webContent, "Route::resource('" . $pluralModel . "', " . $ucCamelModel . "Controller::class)->only('index', 'show');")) {
                    $webContent = str_replace('// ----- GENERATOR 2 -----', "Route::resource('" . $pluralModel . "', " . $ucCamelModel . "Controller::class)->only('index', 'show');" . PHP_EOL . "\t\t// ----- GENERATOR 2 -----", $webContent);
                }
                file_put_contents("routes" . DIRECTORY_SEPARATOR . "web.php", $webContent);
            }

            //---------- Events ----------
            Artisan::call("make:senses_created_event {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/{$ucCamelModel}Created.php");
            Artisan::call("make:senses_updated_event {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/{$ucCamelModel}Updated.php");
            Artisan::call("make:senses_deleted_event {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/{$ucCamelModel}Deleted.php");

            //---------- Observer ----------
            Artisan::call("make:senses_observer {$model} --force=" . $force . " --model={$model} --path={$ucCamelModel}Observer.php");
            $content = file_get_contents("app/Providers/AppServiceProvider.php");
            $content1 = "";
            if (!str_contains($content, "use App\Observers\\" . $ucCamelModel . "Observer;")) {
                $content1 = "use App\Observers\\" . $ucCamelModel . "Observer;";
            }
            if (!str_contains($content, "use App\Models\\" . $ucCamelModel . ";")) {
                $content1 .= PHP_EOL . "use App\Models\\" . $ucCamelModel . ";";
            }
            $content = str_replace('// ----- GENERATOR 1 -----', $content1 . PHP_EOL . "// ----- GENERATOR 1 -----", $content);
            if (!str_contains($content, $ucCamelModel . "::observe(" . $ucCamelModel . "Observer::class);")) {
                $content = str_replace('// ----- GENERATOR 2 -----', $ucCamelModel . "::observe(" . $ucCamelModel . "Observer::class);" . PHP_EOL . "\t\t// ----- GENERATOR 2 -----", $content);
            }
            file_put_contents("app" . DIRECTORY_SEPARATOR . "Providers" . DIRECTORY_SEPARATOR . "AppServiceProvider.php", $content);
            if ($type !== 'all') {
                $this->info('Generated observer successfully');
            }

            //---------- Actions ----------
            Artisan::call("make:senses_create_action {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/Create{$ucCamelModel}.php");
            Artisan::call("make:senses_update_action {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/Update{$ucCamelModel}.php");
            Artisan::call("make:senses_delete_action {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/Delete{$ucCamelModel}.php");
            Artisan::call("make:senses_generate_show_cache_action {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/Generate{$ucCamelModel}ShowCache.php");
            if ($type !== 'all') {
                $this->info('Generated actions successfully');
            }

            //---------- Requests ----------
            Artisan::call("make:senses_create_request {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/Create{$ucCamelModel}Request.php");
            Artisan::call("make:senses_update_request {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/Update{$ucCamelModel}Request.php");
            Artisan::call("make:senses_list_request {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/List{$ucCamelModel}Request.php");
            Artisan::call("make:senses_show_request {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/Show{$ucCamelModel}Request.php");
            Artisan::call("make:senses_delete_request {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/Delete{$ucCamelModel}Request.php");
            Artisan::call("make:senses_lock_request {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/Lock{$ucCamelModel}Request.php");
            Artisan::call("make:senses_hide_request {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/Hide{$ucCamelModel}Request.php");
            if ($type !== 'all') {
                $this->info('Generated requests successfully');
            }

            //---------- Factory ----------
            Artisan::call("make:senses_factory {$model} --force=" . $force . " --model={$model} --path={$ucCamelModel}Factory.php");
            if ($type !== 'all') {
                $this->info('Generated factory successfully');
            }

            //---------- Seeder ----------
            Artisan::call("make:senses_basic_seeder {$model} --force=" . $force . " --model={$model} --path=Basic{$ucCamelPluralModel}TableSeeder.php");
            $content = file_get_contents("database" . DIRECTORY_SEPARATOR . "seeders" . DIRECTORY_SEPARATOR . "Basic" . DIRECTORY_SEPARATOR . "BasicSensesSeeder.php");
            if (!str_contains($content, "Basic" . $ucCamelPluralModel . "TableSeeder::class")) {
                $content = str_replace('// ----- GENERATOR -----', "Basic" . $ucCamelPluralModel . "TableSeeder::class," . PHP_EOL . " \t\t\t// ----- GENERATOR -----", $content);
            }
            file_put_contents("database" . DIRECTORY_SEPARATOR . "seeders" . DIRECTORY_SEPARATOR . "Basic" . DIRECTORY_SEPARATOR . "BasicSensesSeeder.php", $content);
            if ($type !== 'all') {
                $this->info('Generated basic seeder successfully');
            }

            //---------- Feature Tests ----------
            // Artisan::call("make:senses_feature_test {$model} --force=" . $force . " --model={$model} --path={$ucCamelModel}Test.php");
            // if ($type !== 'all') {
            //     $this->info('Generated feature tests successfully');
            // }

            // if ($jsonData['frontend']) {
            //     //---------- Browser Tests ----------
            //     Artisan::call("make:senses_browser_test {$model} --force=" . $force . " --model={$model} --path={$ucCamelModel}Test.php");
            //     if ($type !== 'all') {
            //         $this->info('Generated browser tests successfully');
            //     }
            // }

            //            //---------- Resource ----------
            //            Artisan::call("make:senses_resource {$model} --force=" . $force . " --model={$model} --path={$ucCamelModel}Resource.php");
            //            if ($type !== 'all') {
            //                $this->info('Generated resource successfully');
            //            }

            if ($jsonData['frontend']) {
                //---------- Blade ----------
                Artisan::call("make:senses_index_blade {$model} --force=" . $force . " --model={$model} --path={$pluralModel}/index.blade.php");
                Artisan::call("make:senses_show_blade {$model} --force=" . $force . " --model={$model} --path={$pluralModel}/show.blade.php");
                if ($type !== 'all') {
                    $this->info('Generated blade files successfully');
                }

                //---------- Vue ----------
                Artisan::call("make:senses_form_vue {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/{$ucCamelModel}Form.vue");
                Artisan::call("make:senses_table_vue {$model} --force=" . $force . " --model={$model} --path={$ucCamelPluralModel}/{$ucCamelModel}Table.vue");

                //                $relationships = array_merge(array_values($jsonData['hasOne']), array_values($jsonData['belongsTo']), array_values($jsonData['hasMany']), array_values($jsonData['hasOneThrough']), array_values($jsonData['hasManyThrough']), array_values($jsonData['morphMany']), array_values($jsonData['morphToMany']), array_values($jsonData['morphOne']), array_values($jsonData['morphedByMany']));
                //                foreach ($relationships as $relationship) {
                //                    if (!is_array($relationship)) {
                //                        $relationshipModel = $relationship;
                //                    } else {
                //                        $relationshipModel = $relationship['relation'];
                //                    }
                //                    $relModel = $relationshipModel; //assignment-group
                //                    $ucCamelRelModel = ucfirst(Str::camel($relationshipModel)); //AssignmentGroup
                //                    Artisan::call("make:senses_relationship_table_vue {$model} --force=" . $force . " --model={$model} --relModel={$relModel} --path={$ucCamelPluralModel}/{$ucCamelRelModel}{$ucCamelPluralModel}Table.vue");
                //                }
                if ($type !== 'all') {
                    $this->info('Generated vue files successfully');
                }
            }

            //---------- Permissions ----------
            $content = file_get_contents("app" . DIRECTORY_SEPARATOR . "Actions" . DIRECTORY_SEPARATOR . "Abilities" . DIRECTORY_SEPARATOR . "GenerateAbilities.php");
            if (!str_contains($content, "'" . $model . "'")) {
                $content = str_replace("\t\t\t// ----- GENERATOR -----", "\t\t\t'" . $model . "'," . PHP_EOL . "\t\t\t// ----- GENERATOR -----", $content);
            }
            file_put_contents("app" . DIRECTORY_SEPARATOR . "Actions" . DIRECTORY_SEPARATOR . "Abilities" . DIRECTORY_SEPARATOR . "GenerateAbilities.php", $content);

            if ($jsonData['frontend']) {
                //---------- Index ----------
                $content = file_get_contents("resources" . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "Components" . DIRECTORY_SEPARATOR . "index.js");
                if (!str_contains($content, "/" . $ucCamelModel . "Table.vue")) {
                    $content = str_replace('// ----- GENERATOR A -----', 'import ' . $ucCamelModel . 'Table from \'./Senses/' . $ucCamelPluralModel . '/' . $ucCamelModel . 'Table.vue\';' . PHP_EOL . 'import ' . $ucCamelModel . 'Form from \'./Senses/' . $ucCamelPluralModel . '/' . $ucCamelModel . 'Form.vue\';' . PHP_EOL . "// ----- GENERATOR A -----", $content);
                    $content = str_replace("\t// ----- GENERATOR B -----", "\t" . $ucCamelModel . 'Table,' . PHP_EOL . "\t" . $ucCamelModel . 'Form,' . PHP_EOL . "\t// ----- GENERATOR B -----", $content);
                }
                file_put_contents("resources" . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "Components" . DIRECTORY_SEPARATOR . "index.js", $content);

                //---------- Sidebar ----------
                $content = file_get_contents("resources" . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "Components" . DIRECTORY_SEPARATOR . "Senses" . DIRECTORY_SEPARATOR . "Sidebar" . DIRECTORY_SEPARATOR . "Sidebar.vue");

                $sidebarLink = "<SidebarItem title=\"" . $sentencePluralModel . "\" v-if=\"user().can('list-" . $model . "')\" to=\"/". $pluralModel . "\" :active=\"Route.is('". $pluralModel . "', 'any')\" />";

                $content = str_replace("<!-- ---------- GENERATOR ---------- -->", $sidebarLink . PHP_EOL . "\t\t\t\t<!-- ---------- GENERATOR ---------- -->", $content);

                file_put_contents("resources" . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "Components" . DIRECTORY_SEPARATOR . "Senses" . DIRECTORY_SEPARATOR . "Sidebar" . DIRECTORY_SEPARATOR . "Sidebar.vue", $content);

                //todo currently temporary, appends to the Layout.vue sidebar - needs moving to separate component
                //removed 19/01/22 as sidebar is now less generic
                //                $content = file_get_contents("resources/js/Components/Senses/Sidebar.vue");
                //
                //                $sidebarLink = '<a href="/' . $pluralModel . '"
                //                               class="flex items-center px-2 py-2 font-medium text-zinc-300 rounded-md hover:bg-primary-700 hover:text-white group">
                //                                <StarIcon class="w-6 h-6 mr-3 text-zinc-400 group-hover:text-zinc-300"/>
                //                                ' . $sentencePluralModel . '
                //                            </a>
                //                            <!-- ---------- GENERATOR ---------- -->
                //                            ';
                //                if (!str_contains($content, 'href="/' . $pluralModel)) {
                //                    $content = str_replace('<!-- ---------- GENERATOR ---------- -->', $sidebarLink, $content);
                //                }
                //                file_put_contents("resources/js/Components/Layout/Layout.vue", $content);
            }

            //---------- Pivot Migration ----------
            foreach ($jsonData['belongsToMany'] as $relationship) {
                $now = Carbon::now();
                $secondDateTime = $now->format('Y_m_d') . '_' . $now->format('u');
                $array = [$snakeModel, str_replace('-', '_', ($relationship))];
                $arrayAlphabetical = $array;
                sort($arrayAlphabetical);
                $files = scandir('database' . DIRECTORY_SEPARATOR . 'migrations');
                $name = "_create_" . $arrayAlphabetical[0] . "_" . $arrayAlphabetical[1] . "_table.php";
                $results = array_filter($files, function ($file) use ($name) {
                    return strpos($file, $name) !== false;
                });
                $delete = false;
                if ($force) {
                    $delete = true;
                } else if (count($results) > 0) {
                    $this->warn('Migrations already exist for the ' . $arrayAlphabetical[0] . "_" . $arrayAlphabetical[1] . ' table.');
                    if ($this->confirm('Do you want to delete the other ' . $arrayAlphabetical[0] . "_" . $arrayAlphabetical[1] . ' migration before you create the new one?')) {
                        $delete = true;
                    } else {
                        $this->warn('Migrating the database will throw errors.');
                    }
                }
                if ($delete) {
                    foreach ($results as $result) {
                        unlink('database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . $result);
                    }
                    if ($type !== 'all') {
                        $this->info('Migrations deleted.');
                    }
                }
                Artisan::call("make:senses_pivot_migration {$arrayAlphabetical[0]} --force=" . $force . " --model={$arrayAlphabetical[0]} --second_model={$arrayAlphabetical[1]} --path={$secondDateTime}_create_{$arrayAlphabetical[0]}_{$arrayAlphabetical[1]}_table.php");
                //                if ($type !== 'all') {
                //                    Schema::dropIfExists($arrayAlphabetical[0] . '_ ' . $arrayAlphabetical[1]);
                //                    Artisan::call("migrate --path=/database/migrations/{$secondDateTime}_create_{$arrayAlphabetical[0]}_{$arrayAlphabetical[1]}_table.php");
                //                }
                if ($type !== 'all') {
                    $this->info('Generated pivot migrations successfully');
                }
            }

            if (!$this->confirm('Would you like to skip setup?')) {
                if ($type !== 'all') {
                    if ($this->confirm('Do you want to migrate the database? If you did not delete duplicate migrations, this will throw errors.')) {
                        $this->info('Migrating database');
                        Schema::dropIfExists($snakePluralModel);
                        //                    Artisan::call("migrate --path=/database/migrations/{$dateTime}_create_{$snakePluralModel}_table.php");
                        Artisan::call("migrate");
                        Artisan::call("db:seed", [
                            '--class' => 'Database\Seeders\Basic\Basic' . $ucCamelPluralModel . 'TableSeeder',
                        ]);
                        $this->info('Database migrated');
                    }
                }

                //            if ($type !== 'all') {
                //                app(GenerateAbilities::class)->execute();
                //                $role = app(GenerateSensesRole::class)->execute();
                //                app(AssignRoleToUser::class)->execute($role, User::find(1));
                //                app(AssignRoleToUser::class)->execute($role, User::find(2));
                //                app(AssignRoleToUser::class)->execute($role, User::find(3));
                //                app(AssignRoleToUser::class)->execute($role, User::find(4));
                //                app(AssignRoleToUser::class)->execute($role, User::find(5));
                //                app(AssignRoleToUser::class)->execute($role, User::find(6));
                //
                //                $this->info('Generated permissions successfully.');
                //            }

                Artisan::call("role:refresh");

                //---------- API Docs ----------
                if ($type !== 'all' && $this->confirm('Do you want to generate the API docs?')) {
                    $this->line('Generating API docs');
                    Artisan::call("generate_scribe");
                    $this->info('API docs generated');
                }

                if ($type !== 'all' && $this->confirm('Do you want to call php artisan cache:clear?')) {
                    $this->line('Clearing cache');
                    Artisan::call("cache:clear");
                    $this->info('Cache cleared');
                }

                if ($jsonData['frontend'] && $type !== 'all') {
                    if ($this->confirm('Do you want to build?')) {
                        $this->line('Running yarn build');
                        exec('yarn build');
                        $this->info('Yarn build complete');
                    }
                }

                if ($type !== 'all' && $this->confirm('Do you want to reseed abilities?')) {
                    $this->line('Reseeding abilities');
                    Artisan::call('role:refresh');
                    $this->line('Abilities reseeded');
                }

                if ($type !== 'all') {
                    $this->newLine();
                    $this->info('---------- Generation finished ----------');
                }
            }
        }

        return 0;
    }
}
