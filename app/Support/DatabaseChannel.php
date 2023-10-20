<?php 

namespace App\Support;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;

class DatabaseChannel extends IlluminateDatabaseChannel {
    /**
     * Build an array payload for the DatabaseNotification Model.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array
     */
    protected function buildPayload($notifiable, Notification $notification)
    {
        $payload = Parent::buildPayload($notifiable, $notification);
        $payload['url'] = null;
        if(isset($payload['data']['url'])) {
            $payload['url'] = $payload['data']['url'];
            
            unset($payload['data']['url']);
        }
        
        
        return $payload;
    }
}