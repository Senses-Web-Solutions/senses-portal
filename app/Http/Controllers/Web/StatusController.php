<?php

namespace App\Http\Controllers\Web;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-status')) {
            abort(403);
        }

        return view('models.statuses.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-status')) {
            abort(403);
        }

        return view('models.statuses.show', compact('id'));
    }
}

//Generated 09-10-2023 12:35:29
