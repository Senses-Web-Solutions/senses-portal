<?php

namespace App\Http\Controllers\Web;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-subscription')) {
            abort(403);
        }

        return view('subscriptions.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-subscription')) {
            abort(403);
        }

        return view('subscriptions.show', compact('id'));
    }
}

//Generated 04-11-2023 16:09:38
