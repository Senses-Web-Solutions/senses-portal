<?php

namespace App\Http\Controllers\Web;

class ChatReviewController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-chat-review')) {
            abort(403);
        }

        return view('chat-reviews.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-chat-review')) {
            abort(403);
        }

        return view('chat-reviews.show', compact('id'));
    }
}

//Generated 04-11-2023 16:09:50
