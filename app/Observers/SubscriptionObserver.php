<?php

namespace App\Observers;

use App\Actions\Subscriptions\GenerateSubscriptionShowCache;
use App\Models\Subscription;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\Subscriptions\SubscriptionCreated;
use App\Events\Subscriptions\SubscriptionDeleted;
use App\Events\Subscriptions\SubscriptionUpdated;

class SubscriptionObserver
{
    public function created(Subscription $subscription)
    {
        //app(GenerateSubscriptionShowCache::class)->onQueue('low')->execute($subscription->id);
        broadcast_safely(new SubscriptionCreated($subscription));
    }

    public function updated(Subscription $subscription)
    {
        TaggedCache::forgetWithTag($subscription->cacheKey);
        //app(GenerateSubscriptionShowCache::class)->onQueue('low')->execute($subscription->id);
        broadcast_safely(new SubscriptionUpdated($subscription));
    }

    public function locked(Subscription $subscription)
    {
        TaggedCache::forgetWithTag($subscription->cacheKey);
        //app(GenerateSubscriptionShowCache::class)->onQueue('low')->execute($subscription->id);
        broadcast_safely(new SubscriptionUpdated($subscription));
    }

    public function unlocked(Subscription $subscription)
    {
        TaggedCache::forgetWithTag($subscription->cacheKey);
        //app(GenerateSubscriptionShowCache::class)->onQueue('low')->execute($subscription->id);
        broadcast_safely(new SubscriptionUpdated($subscription));
    }

    public function deleted(Subscription $subscription)
    {
        TaggedCache::forgetWithTag($subscription->cacheKey);
        broadcast_safely(new SubscriptionDeleted($subscription));
    }

    public function restored(Subscription $subscription)
    {
        TaggedCache::forgetWithTag($subscription->cacheKey);
        //app(GenerateSubscriptionShowCache::class)->onQueue('low')->execute($subscription->id);
        broadcast_safely(new SubscriptionUpdated($subscription));
    }

    public function forceDeleted(Subscription $subscription)
    {
        TaggedCache::forgetWithTag($subscription->cacheKey);
        broadcast_safely(new SubscriptionDeleted($subscription));
    }
}

//Generated 04-11-2023 16:09:38
