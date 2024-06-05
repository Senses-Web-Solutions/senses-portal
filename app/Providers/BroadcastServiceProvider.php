<?php

namespace App\Providers;


use Pusher\Pusher;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Psr\Log\LoggerInterface;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(BroadcastManager $broadcastManager): void
    {
        Broadcast::routes();

        $broadcastManager->extend('pusher', function($app, $config) {
            $pusher = new Pusher(
                $config['key'], $config['secret'],
                $config['app_id'], $config['options'] ?? [],
                new \GuzzleHttp\Client($config['client_options']) ?? []
            );
    
            if ($config['log'] ?? false) {
                $pusher->setLogger($this->app->make(LoggerInterface::class));
            }
    
            return new PusherBroadcaster($pusher);
        });

        require base_path('routes/channels.php');
    }
}
