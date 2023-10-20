<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

class AbilityController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-ability')) {
            abort(403);
        }
        return view('models.abilities.index');
    }
}
