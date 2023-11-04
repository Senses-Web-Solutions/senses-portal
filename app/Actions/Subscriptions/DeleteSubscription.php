<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;
use Spatie\QueueableAction\QueueableAction;

class DeleteSubscription
{
    use QueueableAction;

    public function execute(int $id)
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->delete();

        return $subscription;
    }
}

//Generated 04-11-2023 16:09:38
