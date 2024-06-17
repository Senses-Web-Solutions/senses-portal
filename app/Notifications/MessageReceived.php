<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use App\Traits\SensesNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageReceived extends Notification implements ShouldQueue
{
    use Queueable, SensesNotification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected Message $message)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->message->author . ' Sent You a Message',
            'description' => str($this->message->content)->limit(30),
            // 'user_id' => $this->message->user->id,
        ];
    }
}
