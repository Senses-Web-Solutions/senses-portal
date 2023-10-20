<?php

namespace App\Console\Commands\Senses;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AboutSenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'senses:about';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks Senses setup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return $this->call('about', ['--only' => 'senses']);
    }
}
