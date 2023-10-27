<?php

namespace App\Http\Controllers\Web;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-company')) {
            abort(403);
        }

        return view('companies.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-company')) {
            abort(403);
        }

        return view('companies.show', compact('id'));
    }
}

//Generated 27-10-2023 10:55:44
