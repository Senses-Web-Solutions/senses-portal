<?php

namespace App\Http\Controllers\Web;

use App\Models\ServerMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServerMetricController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-server-metric')) {
            abort(403);
        }

        return view('server-metrics.index');
    }

    public function show($id)
    {
        if (!getCurrentUser()->can('show-server-metric')) {
            abort(403);
        }

        return view('server-metrics.show', compact('id'));
    }
}

//Generated 27-10-2023 10:55:27
