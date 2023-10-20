<?php

namespace App\Actions\Users;

use App\Models\User;
use Spatie\QueueableAction\QueueableAction;

class GenerateUserFullName
{
    use QueueableAction;

    /**
     * Execute the action.
     *
     * @return mixed
     */
    public function execute(User $user)
    {
        if (!isset($user->first_name) || !isset($user->last_name)) {
            return null;
        }

        $fullName = ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);
        if(strlen(trim($fullName)) == 0 || ($user->first_name == 'Unknown' && $user->last_name == 'Unknown')) {
            return 'Unknown';
        }
        return $fullName;
    }
}
