<?php

namespace App\Http\Controllers\Web;

use App\Models\BlockGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockGroupController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-block-group')) {
            abort(403);
        }

        return view('models.block-groups.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-block-group')) {
            abort(403);
        }

        return view('models.block-groups.show', compact('id'));
    }
}

//Generated 16-10-2023 10:39:10
