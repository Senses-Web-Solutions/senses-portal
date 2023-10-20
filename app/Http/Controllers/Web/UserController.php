<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-user')) {
            abort(403);
        }

        return view('models.users.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-user')) {
            abort(403);
        }

        return view('models.users.show', compact('id'));
    }
}

//Generated 10-10-2023 10:05:12
