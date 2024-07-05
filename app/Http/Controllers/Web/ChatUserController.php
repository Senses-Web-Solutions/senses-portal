<?php

namespace App\Http\Controllers\Web;

class ChatUserController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-chat-user')) {
            abort(403);
        }

        return view('chat-users.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-chat-user')) {
            abort(403);
        }

        return view('chat-users.show', compact('id'));
    }
}

//Generated 04-11-2023 16:09:50
