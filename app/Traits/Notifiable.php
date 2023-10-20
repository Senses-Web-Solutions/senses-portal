<?php

namespace App\Traits;

use App\Models\Notification;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\Notifiable as IlluminateNotifiable;
use Illuminate\Support\Str;

trait Notifiable {
    use IlluminateNotifiable;

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    public function notify($instance)
    {
        // Override for the default illumatenotifiable to check the user settings before sending
        // ToDo: this needs changing to look at sms and email but idk how those work yet so

        if (!($this->userSettings->where('setting', "contact-settings")->first()->data[Str::kebab(class_basename($instance))]["web"] ?? true)) {
            // echo 'User doesnt not wish to recieve this notification';
        } else {
            app(Dispatcher::class)->send($this, $instance);
        }
    }
}
