<?php

namespace App\Actions\Messages;

class StopTypingMessage
{
    public function execute($request)
    {
        $data = $request->all();
        $chatID = $data['chat_id'];
        $name = $data['name'];
        $fromAgent = $data['from_agent'];
        broadcast_safely(new \App\Events\Chats\StopTyping($chatID, $name, $fromAgent));

        return response()->json(['chat_id' => $chatID, 'name' => $name, 'from_agent' => $fromAgent]);
    }
}

//Generated 27-10-2023 10:55:45
