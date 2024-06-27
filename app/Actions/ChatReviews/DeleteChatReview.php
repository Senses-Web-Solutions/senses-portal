<?php

namespace App\Actions\ChatReviews;

use App\Models\ChatReview;
use Spatie\QueueableAction\QueueableAction;

class DeleteChatReview
{
    use QueueableAction;

    public function execute(int $id)
    {
        $chatReview = ChatReview::findOrFail($id);

        $chatReview->delete();

        return $chatReview;
    }
}

//Generated 27-10-2023 10:55:45
