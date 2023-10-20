<?php

namespace App\Console\Commands\Senses;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class GenerateScribe extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate_scribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes relevant file and generates scribe docs';

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
     * @return mixed
     */
    public function handle()
    {
        Artisan::call("cache:clear");
        if (file_exists('resources/docs/.filemtimes')) {
            unlink('resources/docs/.filemtimes');
        }
        Artisan::call("cache:clear");
        Artisan::call("scribe:generate --force --verbose");
        Artisan::call("cache:clear");
    }
}
