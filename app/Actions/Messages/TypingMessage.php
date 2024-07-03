<?php

namespace App\Actions\Messages;

class TypingMessage
{
    public function execute($request)
    {
        $data = $request->all();
        $chatID = $data['chat_id'];
        $fromAgent = $data['from_agent'];
        broadcast_safely(new \App\Events\Chats\Typing($chatID, $fromAgent));

        return response()->json(['chat_id' => $chatID, 'from_agent' => $fromAgent]);
    }
}

//Generated 27-10-2023 10:55:45
