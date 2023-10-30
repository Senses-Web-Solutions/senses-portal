<?php

namespace App\Console\Commands;

use App\Support\DeployHQ;
use Illuminate\Console\Command;

class GetActiveDeploymentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:latest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scripts to run after deploy.';

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
        $deploy = new DeployHQ();

        $response = $deploy->getDeployments('senses-portal');

        foreach ($response['records'] as $record) {
            if ($record['status'] == 'running') {
                $this->info("Deployment is running");
            }
        }
    }
}
