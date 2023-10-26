<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:run';

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
        $this->call('migrate', ['--force' => true]);
        $this->call('horizon:terminate');
        $this->call('horizon:publish');
        $this->call('queue:restart');
        $this->call('vendor:publish', ['--tag' => 'log-viewer-assets', '--force' => true]);

        return 0;
    }
}
