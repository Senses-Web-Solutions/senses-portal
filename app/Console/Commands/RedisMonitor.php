<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\RedisWarning;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Notification;

class RedisMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check redis is running smoothly';

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

        $output = Redis::connection()->command('info');
        if(isset($output['Memory'])) {
            $output = $output['Memory']; //older redis for local?
        }

        if( $output['maxmemory'] == 0) {
            logger('redis max memory not set : ' . $output['maxmemory']);
            return 0;
        }

        $memoryPercentage = round(($output['used_memory'] / $output['maxmemory']) * 100, 2);
            
        $this->comment('Memory usage: ' . $memoryPercentage . '%');

        if($memoryPercentage > 85) {
            Notification::route('slack', 'https://hooks.slack.com/services/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')
            ->notify(new RedisWarning($memoryPercentage, $output['used_memory_human'], $output['maxmemory_human']));
        }

        return 0;
    }
}
