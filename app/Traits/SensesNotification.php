<?php
namespace App\Traits;

use NotificationChannels\Fcm\FcmChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;


trait SensesNotification
{
    /**
     * Determine which queues should be used for each notification channel.
     *
     * @return array
     */
    public function viaQueues()
    {
        return [
            'database'          => 'critical',
            'broadcast'         => 'critical',
            'mail'              => 'critical',
            'sms'               => 'critical',
            FcmChannel::class   => 'critical',
        ];
    }

    public function shouldSendFromConfig() {
        $shouldSend = config('client.notifications.'. self::class, false);
        if(!$shouldSend) {
            logger('Client config prevented notification for ' . class_basename($this) . ' from sending');
        }

        return $shouldSend;
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        if(!in_array('mail', $this->via($notifiable))) {
            return true;
        }
        return $this->shouldSendFromConfig(); //please ensure clients/base/config.php as well the relevant client configs have the class added if it needs to be true by default.
    }

    public function toBroadcast($notifiable)
    {
        $data = [];
        if(method_exists($this, 'toArray')) {
            $data = $this->toArray($notifiable);
        }
        return (new BroadcastMessage($data))->onQueue('critical');
    }
}
