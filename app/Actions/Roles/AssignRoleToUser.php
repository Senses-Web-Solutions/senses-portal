<?php

namespace App\Actions\Roles;

use App\Models\User;
use App\Models\Ability;
use Illuminate\Support\Str;
use Silber\Bouncer\Bouncer;
use App\Models\AbilityGroup;
use Silber\Bouncer\Database\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\QueueableAction\QueueableAction;

class AssignRoleToUser
{
    use QueueableAction;


    /**
     * Create a new action instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the action.
     *
     * @return mixed
     */
    public function execute(Role $role, User $user)
    {
        $user->assign($role->name);
        $user->emitRolesUpdated();
    }
}
