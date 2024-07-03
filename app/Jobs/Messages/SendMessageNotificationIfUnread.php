<?php

namespace App\Jobs;

use App\Models\Message;
use App\Notifications\MessageReceived;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageNotificationIfUnread implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected Message $message)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // If a user hasn't read their message in ~20s (the delay this job is) or more, send them a notification.
        $agents = $this->message->chat->agents;

        foreach ($agents as $agent) {
            if ($this->message->hasBeenReadBy($agent->id) || $this->message->author->full_name == $agent->full_name) {
                continue;
            }


            if ($agent) {
                $agent->notify(new MessageReceived($this->message));
            }
        }
        return;
    }
}
