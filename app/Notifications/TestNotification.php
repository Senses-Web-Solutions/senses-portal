<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use App\Traits\SensesNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable, SensesNotification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
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
            'title' => 'Test Notification',
            'description' => 'This is just a test notification to test something about notifications.',
        ];
    }
}
