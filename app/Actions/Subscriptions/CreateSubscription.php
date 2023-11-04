<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;
use Spatie\QueueableAction\QueueableAction;

class CreateSubscription
{
    use QueueableAction;

    public function execute(array $data)
    {
        $subscription = new Subscription($data);

		if(isset($data['company_id'])) {
			$subscription->company()->associate($data['company_id']);
		}

        $subscription->save();

        return $subscription;
    }
}

//Generated 04-11-2023 16:09:38
