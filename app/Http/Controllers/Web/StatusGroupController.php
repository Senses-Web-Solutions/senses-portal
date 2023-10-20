<?php

namespace App\Http\Controllers\Web;

use App\Models\StatusGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusGroupController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-status-group')) {
            abort(403);
        }

        return view('models.status-groups.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-status-group')) {
            abort(403);
        }

        return view('models.status-groups.show', compact('id'));
    }
}

//Generated 09-10-2023 12:05:02
