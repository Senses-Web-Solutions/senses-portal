<?php

namespace App\Console\Commands\Roles;


use Bouncer;
use Illuminate\Console\Command;

class RefreshRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:refresh {--R|role-only} {--seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh generated roles and clear cache';

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
        if (!$this->option('role-only')) {
            $this->call('ability:generate');
            $this->call('role:generate', ['role' => 'senses']);
            // $this->call('role:generate', ['role' => 'super-admin']);
            // $this->call('role:generate', ['role' => 'engineer']);

            // if($this->option('seed')) {
            //     $this->call('role:generate', ['role' => 'office']);
            //     $this->call('role:generate', ['role' => 'remote-engineer']);
            //     $this->call('role:generate', ['role' => 'sales']);
            //     $this->call('role:generate', ['role' => 'operations']);
            //     $this->call('role:generate', ['role' => 'finance']);
            //     $this->call('role:generate', ['role' => 'commercial']);
            //     $this->call('role:generate', ['role' => 'hr']);
            //     $this->call('role:generate', ['role' => 'p-t']);
            //     $this->call('role:generate', ['role' => 'hseq']);
            //     $this->call('role:generate', ['role' => 'depot-manager']);
            //     $this->call('role:generate', ['role' => 'regional-manager']);
            //     $this->call('role:generate', ['role' => 'client']);
            //     $this->call('role:generate', ['role' => 'admin']);
            //     $this->call('role:generate', ['role' => 'core-operations']);
            // }
        }

        Bouncer::refresh();
        $this->info('Role cache cleared.');
        return 0;
    }
}
