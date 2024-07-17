<?php

namespace App\Actions\Chats;

use App\Actions\ActionLogs\CreateActionLog;
use App\Events\Chats\ChatUpdated;
use App\Models\Chat;
use Spatie\QueueableAction\QueueableAction;

class UpdateChatCurrentPage
{
    use QueueableAction;

    public function execute(Chat|int $chat, string $currentPage)
    {
        if (is_int($chat)) {
            $chat = Chat::findOrFail($chat);
        }

        if ($chat->current_page !== $currentPage) {
            $oldPage = $chat->current_page;
            $chat->current_page = $currentPage;
            app(CreateActionLog::class)->onQueue()->execute($chat, 'chat-user-navigated-to', ['old_page' => $oldPage, 'new_page' => $currentPage]);
            $chat->save();
        }

        return $chat;
    }
}
