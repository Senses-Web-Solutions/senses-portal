<?php

namespace App\Actions\Roles;

use Bouncer;
use App\Enums\LockType;
use App\Models\Ability;
use Spatie\QueueableAction\QueueableAction;

class GenerateSensesRole
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
    public function execute()
    {
        $role = Bouncer::role()::firstOrNew([
            'name' => 'senses',
            'title' => 'Senses',
        ],[
            'locked_at' => now(),
            'lock_type' => LockType::CORE
        ]);

        $role->saveQuietly();

        Bouncer::sync($role)->abilities(Ability::pluck('id')->toArray());        
        Bouncer::refreshFor($role);
        $role->touch(); //trigger updates

        return $role;
    }
}
