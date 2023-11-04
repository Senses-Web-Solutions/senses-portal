<?php

namespace App\Http\Controllers\Web;

use App\Models\CommunicationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunicationLogController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-communication-log')) {
            abort(403);
        }

        return view('communication-logs.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-communication-log')) {
            abort(403);
        }

        return view('communication-logs.show', compact('id'));
    }
}

//Generated 04-11-2023 16:09:50
