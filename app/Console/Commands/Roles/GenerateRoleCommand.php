<?php

namespace App\Console\Commands\Roles;

use App\Actions\Roles\GenerateAdminRole;
use App\Actions\Roles\GenerateClientRole;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Actions\Roles\GenerateHRRole;
use App\Actions\Roles\GenerateHSEQRole;
use App\Actions\Roles\GenerateSalesRole;
use App\Actions\Roles\GenerateOfficeRole;
use App\Actions\Roles\GenerateSensesRole;
use App\Actions\Roles\GenerateFinanceRole;
use App\Actions\Roles\GenerateEngineerRole;
use App\Actions\Roles\GenerateCommercialRole;
use App\Actions\Roles\GenerateCoreOperationsRole;
use App\Actions\Roles\GenerateOperationsRole;
use App\Actions\Roles\GenerateSuperAdminRole;
use App\Actions\Roles\GenerateDepotManagerRole;
use App\Actions\Roles\GenerateRemoteEngineerRole;
use App\Actions\Roles\GenerateRegionalManagerRole;
use App\Actions\Roles\GeneratePlantAndTransportRole;

class GenerateRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:generate {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate roles';

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
        $role = $this->argument('role');
        match ($role) {
            'senses' => app(GenerateSensesRole::class)->execute(),
            // 'engineer' => app(GenerateEngineerRole::class)->execute(),
            // 'super-admin' => app(GenerateSuperAdminRole::class)->execute(),
            // 'office' => app(GenerateOfficeRole::class)->execute(),
            // 'remote-engineer' => app(GenerateRemoteEngineerRole::class)->execute(),
            // 'sales' => app(GenerateSalesRole::class)->execute(),
            // 'operations' => app(GenerateOperationsRole::class)->execute(),
            // 'commercial' => app(GenerateCommercialRole::class)->execute(),
            // 'finance' => app(GenerateFinanceRole::class)->execute(),
            // 'p-t' => app(GeneratePlantAndTransportRole::class)->execute(),
            // 'hr' => app(GenerateHRRole::class)->execute(),
            // 'hseq' => app(GenerateHSEQRole::class)->execute(),
            // 'depot-manager' => app(GenerateDepotManagerRole::class)->execute(),
            // 'regional-manager' => app(GenerateRegionalManagerRole::class)->execute(),
            // 'client' => app(GenerateClientRole::class)->execute(),
            // 'admin' => app(GenerateAdminRole::class)->execute(),
            // 'core-operations' => app(GenerateCoreOperationsRole::class)->execute(),
            default => $this->error('Role not found'),
        };


        $this->info(Str::title($role) . ' role generated');
        return 0;
    }
}
