<?php

namespace App\Http\Controllers\Web;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServerController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-server')) {
            abort(403);
        }

        return view('servers.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-server')) {
            abort(403);
        }

        return view('servers.show', compact('id'));
    }
}

//Generated 27-10-2023 10:53:42
