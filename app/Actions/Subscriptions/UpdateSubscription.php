<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;
use Spatie\QueueableAction\QueueableAction;

class UpdateSubscription
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->fill($data);

		if(isset($data['company_id'])) {
			$subscription->company()->associate($data['company_id']);
		}

        if (!$subscription->isDirty()) {
            $subscription->emitUpdated();
        }

        $subscription->save();

        return $subscription;
    }
}

//Generated 04-11-2023 16:09:38
