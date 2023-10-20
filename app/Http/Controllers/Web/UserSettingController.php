<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSettingController extends Controller
{
    public function index()
    {
        if (!getCurrentUser()->can('list-user-setting')) {
            abort(403);
        }
        return view('models.user-settings.index');
    }

    public function show($id)
    {
        if (
            !getCurrentUser()->can('show-user-setting')
        ) {
            abort(403);
        }
        return view('models.user-settings.show', compact('id'));
    }
}

//Generated 07-09-2021 09:54:24
