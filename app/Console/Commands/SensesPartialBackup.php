<?php

namespace App\Console\Commands;

use App\Support\PartialBackup;
use Illuminate\Console\Command;


class SensesPartialBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'senses-backup:run-partial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a partial backup of the database.';

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
        $backup = new PartialBackup($this);
        $backup->run();
        
        return 0;
    }

}
