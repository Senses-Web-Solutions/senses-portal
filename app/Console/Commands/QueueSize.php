<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class QueueSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:size';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List size of queues';

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
        $queues = ['critical', 'default', 'low', 'notifications'];
        $queueSizes = [];

        foreach($queues as $queue) {
            array_push($queueSizes, [$queue, Queue::size($queue)]);
        }

        $this->table(
            ['Queue', 'Size'],
            $queueSizes,
            'box'
        );

        return 0;
    }
}
