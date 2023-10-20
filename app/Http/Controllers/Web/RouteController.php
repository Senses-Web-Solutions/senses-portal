<?php

namespace App\Http\Controllers\Web;

use App\Models\Page;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class RouteController extends Controller
{

    public function show($slug)
    {
        //todo hook into aliases to avoid multiple model queries for what slug is what?
        $page = Page::where('slug', $slug)->firstOrFail();
        
        return view()->client('pages.show', compact('page'));
    }
}

//Generated 10-10-2023 10:20:22
