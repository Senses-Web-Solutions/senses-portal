<?php

namespace App\Actions\ChatReviews;

use App\Models\Chat;
use App\Models\ChatReview;
use App\Models\Status;
use Spatie\QueueableAction\QueueableAction;

class CreateChatReview
{
    use QueueableAction;

    public function execute(array $data)
    {
        $data['overall'] = round(($data['knowledge'] + $data['friendliness'] + $data['responsiveness']) / 3, 1);
        $chatReview = new ChatReview($data);

        $chat = Chat::find($data['chat_id']);

        $chatReview->chat()->associate($chat);
        $chatReview->chatUser()->associate($chat->chat_user_id);

        if ($data['resolved'] == false) {
            $status = Status::where('slug', 'unresolved')->first();
            $chat->status()->associate($status);
            $chat->save();
        }

        $chatReview->save();

        return $chatReview;
    }
}

//Generated 27-10-2023 10:55:45
