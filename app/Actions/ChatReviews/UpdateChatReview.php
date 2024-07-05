<?php

namespace App\Actions\ChatReviews;

use App\Models\Chat;
use App\Models\ChatReview;
use App\Models\Status;
use Spatie\QueueableAction\QueueableAction;

class UpdateChatReview
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $chatReview = ChatReview::findOrFail($id);

        $chatReview->fill($data);

        $chat = Chat::find($data['chat_id']);

        $chatReview->chat()->associate($chat);
        $chatReview->chatUser()->associate($chat->chat_user_id);

        if ($data['resolved'] === false) {
            $status = Status::where('slug', 'unresolved')->first();
            $chat->status()->associate($status);
            $chat->save();
        }

        if (!$chatReview->isDirty()) {
            $chatReview->emitUpdated();
        }

        $chatReview->save();

        return $chatReview;
    }
}

//Generated 27-10-2023 10:55:45
