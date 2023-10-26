<?php

namespace App\Support;

use Throwable;

class Dispatcher extends \Illuminate\Bus\Dispatcher {

    public function __construct($container = null, \Illuminate\Bus\Dispatcher $dispatcher) {
        return parent::__construct($container, $dispatcher->queueResolver);
    }

    public function dispatchToQueue($command)
    {

        $name = match($command::class) {
            \Illuminate\Queue\CallQueuedClosure::class => 'closure',
            default => $command::class
        };

        senses_log('jobs', '[dispatch] ' . $name . ' => ' . now()->format('d-m-Y H:i:s'), force:true);

        try {
            return parent::dispatchToQueue($command);
        }
        catch(Throwable $e) {
            if($command->connection == 'overflow') {
                throw $e;
            }

            $command->connection = 'overflow';
            return parent::dispatchToQueue($command);
        }
    }
}
