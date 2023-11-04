<?php

namespace App\Http\Controllers\Web;

use App\Models\Revenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RevenueController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-revenue')) {
            abort(403);
        }

        return view('revenues.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-revenue')) {
            abort(403);
        }

        return view('revenues.show', compact('id'));
    }
}

//Generated 04-11-2023 16:09:26
