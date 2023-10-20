<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

class AbilityGroupController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-ability-group')) {
            abort(403);
        }
        return view('models.ability-groups.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-ability-group')) {
            abort(403);
        }
        return view('models.ability-groups.show', compact('id'));

    }
}
