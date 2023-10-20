<?php

namespace App\Http\Controllers\Web;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function create() {
        if (!getCurrentUser()->can('create-page')) {
            abort(403);
        }

        return view('models.pages.builder');
    }

    public function edit($id) {
        if (!getCurrentUser()->can('update-page')) {
            abort(403);
        }

        return view('models.pages.builder', ['id' => $id]);
    }

    public function index()
    {
        if (!getCurrentUser()->can('list-page')) {
            abort(403);
        }

        return view('models.pages.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-page')) {
            abort(403);
        }

        return view('models.pages.show', compact('id'));
    }
}

//Generated 10-10-2023 14:43:35
