<?php

namespace App\Http\Controllers\Web;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-tag')) {
            abort(403);
        }

        return view('models.tags.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-tag')) {
            abort(403);
        }

        return view('models.tags.show', compact('id'));
    }
}

//Generated 09-10-2023 10:18:19
