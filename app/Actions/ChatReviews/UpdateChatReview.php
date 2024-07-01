<?php

namespace App\Actions\ChatReviews;

use App\Models\ChatReview;
use Spatie\QueueableAction\QueueableAction;

class UpdateChatReview
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $chatReview = ChatReview::findOrFail($id);

        $chatReview->fill($data);

        $chatReview->chat()->associate($data['chat_id']);

        if (!$chatReview->isDirty()) {
            $chatReview->emitUpdated();
        }

        $chatReview->save();

        return $chatReview;
    }
}

//Generated 27-10-2023 10:55:45
