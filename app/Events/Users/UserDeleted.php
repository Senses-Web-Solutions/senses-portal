<?php

namespace App\Events\Users;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UserDeleted implements ShouldBroadcastNow
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('users.' . $this->user->id . '.main')];
    }
}
