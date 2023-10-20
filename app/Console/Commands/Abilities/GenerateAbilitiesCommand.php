<?php

namespace App\Console\Commands\Abilities;

use Illuminate\Console\Command;
use App\Actions\Abilities\GenerateAbilities;

class GenerateAbilitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ability:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate abilties';

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
        app(GenerateAbilities::class)->execute();
        $this->info('Abilities generated');
        return 0;
    }
}
