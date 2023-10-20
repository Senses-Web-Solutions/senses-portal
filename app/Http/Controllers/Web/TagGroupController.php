<?php

namespace App\Http\Controllers\Web;

use App\Models\TagGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagGroupController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-tag-group')) {
            abort(403);
        }

        return view('models.tag-groups.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-tag-group')) {
            abort(403);
        }

        return view('models.tag-groups.show', compact('id'));
    }
}

//Generated 09-10-2023 10:26:55
