<?php

namespace App\Actions\ChatReviews;

use App\Models\ChatReview;
use Spatie\QueueableAction\QueueableAction;

class CreateChatReview
{
    use QueueableAction;

    public function execute(array $data)
    {
        $data['overall'] = ($data['knowledge'] + $data['friendliness'] + $data['responsiveness']) / 3;
        $chatReview = new ChatReview($data);

        if (isset($data['chat_id'])) {
            $chatReview->chat()->associate($data['chat_id']);
        }

        $chatReview->save();

        return $chatReview;
    }
}

//Generated 27-10-2023 10:55:45
