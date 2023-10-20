<?php

namespace App\Observers;

use App\Models\User;
use App\Actions\Users\GenerateUserFullName;
use App\Actions\Users\GenerateUserShowCache;
use Senses\TaggedCache\Facades\TaggedCache;

use App\Events\Users\UserCreated;
use App\Events\Users\UserDeleted;
use App\Events\Users\UserUpdated;

class UserObserver
{
    /**
     * Handle the user "creating" event.
     *
     * @param User $user
     * @return void
     */
    public function creating(User &$user)
    {
        // Update full_name column
        $user->full_name = app(GenerateUserFullName::class)->execute($user);
    }

    /**
     * Handle the user "updating" event.
     *
     * @param User $user
     * @return void
     */
    public function updating(User &$user)
    {
        // Update full_name column
        $user->full_name = app(GenerateUserFullName::class)->execute($user);
    }

    public function created(User $user)
    {
        //app(GenerateUserShowCache::class)->onQueue('low')->execute($user->id);
        broadcast_safely(new UserCreated($user));
    }

    public function updated(User $user)
    {
        TaggedCache::forgetWithTag($user->cacheKey);
        //app(GenerateUserShowCache::class)->onQueue('low')->execute($user->id);
        broadcast_safely(new UserUpdated($user));
    }

    public function locked(User $user)
    {
        TaggedCache::forgetWithTag($user->cacheKey);
        //app(GenerateUserShowCache::class)->onQueue('low')->execute($user->id);
        broadcast_safely(new UserUpdated($user));
    }

    public function unlocked(User $user)
    {
        TaggedCache::forgetWithTag($user->cacheKey);
        //app(GenerateUserShowCache::class)->onQueue('low')->execute($user->id);
        broadcast_safely(new UserUpdated($user));
    }

    public function deleted(User $user)
    {
        TaggedCache::forgetWithTag($user->cacheKey);
        broadcast_safely(new UserDeleted($user));
    }

    public function restored(User $user)
    {
        TaggedCache::forgetWithTag($user->cacheKey);
        //app(GenerateUserShowCache::class)->onQueue('low')->execute($user->id);
        broadcast_safely(new UserUpdated($user));
    }

    public function forceDeleted(User $user)
    {
        TaggedCache::forgetWithTag($user->cacheKey);
        broadcast_safely(new UserDeleted($user));
    }
}

//Generated 10-10-2023 10:05:12
