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

    public function resolved()
    {
        if (!getCurrentUser()->can('list-chat')) {
            abort(403);
        }

        return view('chats.resolved');
    }

    public function unresolved()
    {
        if (!getCurrentUser()->can('list-chat')) {
            abort(403);
        }

        return view('chats.unresolved');
    }

    public function inbox()
    {
        if (!getCurrentUser()->can('list-chat')) {
            abort(403);
        }

        return view('chats.inbox');
    }

    public function history()
    {
        if (!getCurrentUser()->can('list-chat')) {
            abort(403);
        }

        return view('chats.history');
    }

    public function feedback()
    {
        if (!getCurrentUser()->can('list-chat')) {
            abort(403);
        }

        return view('chats.feedback');
    }

    public function setup()
    {
        if (!getCurrentUser()->can('list-chat')) {
            abort(403);
        }

        return view('chats.setup');
    }
}

//Generated 04-11-2023 16:09:50
