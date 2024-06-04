<?php

namespace App\Http\Controllers\Web;

class ChatController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-chat')) {
            abort(403);
        }

        return view('chats.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-chat')) {
            abort(403);
        }

        return view('chats.show', compact('id'));
    }
}

//Generated 04-11-2023 16:09:50
