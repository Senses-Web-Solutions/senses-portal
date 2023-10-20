<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\RouteController;
use App\Http\Controllers\Web\UserController;

use App\Http\Controllers\Web\AbilityController;
use App\Http\Controllers\Web\StatusController;
use App\Http\Controllers\Web\StatusGroupController;
use App\Http\Controllers\Web\TagController;
use App\Http\Controllers\Web\TagGroupController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\BlockGroupController;
use App\Http\Controllers\Web\AbilityGroupController;
// ----- GENERATOR 1 -----

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::pusherBatchAuth();

Route::group(['middleware' => ['auth']], function () {
	Route::view('/', 'dashboard');
	Route::resource('users', UserController::class)->only('index', 'show');

	Route::resource('tags', TagController::class)->only('index', 'show');
	Route::resource('tag-groups', TagGroupController::class)->only('index', 'show');
	Route::resource('statuses', StatusController::class)->only('index', 'show');
	Route::resource('status-groups', StatusGroupController::class)->only('index', 'show');
	Route::resource('ability-groups', AbilityGroupController::class)->only('index', 'show');

	Route::resource('pages', PageController::class)->only('index', 'show', 'create', 'edit');
	Route::resource('block-groups', BlockGroupController::class)->only('index', 'show');

	// ----- GENERATOR 2 -----


	//Abilities
	Route::get('abilities/reseed', [AbilityController::class, 'index']);
});

Route::get('/{slug}', [RouteController::class, 'show']);
