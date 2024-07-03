<?php

namespace App\Actions\ChatUsers;

use App\Models\ChatUser;
use App\Support\SensesUUID;
use Illuminate\Support\Str;
use Spatie\QueueableAction\QueueableAction;

class CreateChatUser
{
    use QueueableAction;

    public function execute(array $data)
    {
        $chatUser = new ChatUser($data);
        $chatUser->uuid = SensesUUID::generate();

        // If no data['last_name'] is provided, then see how many people have the same first name and set the last name to the number of people with the same first name
        if (!isset($data['last_name'])) {
            $chatUser->last_name = ChatUser::where('company_id', $data['company_id'])
                ->where('system', $data['system'])
                ->where('first_name', $data['first_name'])
                ->count() + 1;

            $chatUser->full_name = $data['first_name'] . ' ' . $chatUser->last_name;
        }

        $chatUser->company()->associate($data['company_id']);

        $chatUser->save();

        return $chatUser;
    }
}

//Generated 27-10-2023 10:55:45
