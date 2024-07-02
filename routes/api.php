<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\AbilityController;
use App\Http\Controllers\Api\TagGroupController;
use App\Http\Controllers\Api\StatusGroupController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserSettingController;
use App\Http\Controllers\Api\AbilityGroupController;
use App\Http\Controllers\Api\ActionLogController;
use App\Http\Controllers\Api\AllowedChatSiteController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ServerController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ServerMetricController;
use App\Http\Controllers\Api\RevenueController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\CommunicationLogController;
use App\Http\Controllers\Api\MessageController;

// ----- GENERATOR 1 -----

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:server'])->group(function () {
    Route::sensesApiResources([
        'server-metrics' => ServerMetricController::class,
    ]);

    Route::post('servers/validate', [ServerController::class, 'validateServer']);
});

Route::middleware(['auth:api'])->prefix('v2')->group(function () {
    Route::sensesApiResources([
        'tags' => TagController::class,
        'tag-groups' => TagGroupController::class,
        'statuses' => StatusController::class,
        'status-groups' => StatusGroupController::class,
        'files' => FileController::class,
        'users' => UserController::class,
        'ability-groups' => AbilityGroupController::class,
		'companies' => CompanyController::class,
		'servers' => ServerController::class,
		'revenues' => RevenueController::class,
		'subscriptions' => SubscriptionController::class,
		'communication-logs' => CommunicationLogController::class,
        'chats' => ChatController::class,
        'allowed-chat-sites' => AllowedChatSiteController::class,
        'messages' => MessageController::class,
        'chat-reviews' => ChatReviewController::class,
		// ----- GENERATOR 2 -----
    ]);

    Route::get("users/{user}/popover-content", [UserController::class, "userPopoverContent"]);

    //Abilities
    Route::get("abilities", [AbilityController::class, "index"]);
    Route::post('abilities/reseed', [AbilityController::class, 'reseed']);
    Route::get('ability-groups/{ability_groups}/abilities', [AbilityController::class, 'abilityGroupAbilities']);

    //User Settings
    Route::get('users/{user}/user-settings/{setting}', [UserSettingController::class, 'getUserSetting']);
    Route::delete('users/{user}/user-settings/{setting}', [UserSettingController::class, 'destroy']);
    Route::match(['post', 'put', 'patch'], 'users/{user}/user-settings/{setting}', [UserSettingController::class, 'updateUserSetting']);

    //Notifications
    Route::get('users/{user}/notifications', [NotificationController::class, 'userNotifications']);
    Route::get('users/{user}/notifications/counts', [NotificationController::class, 'userNotificationCounts']);
    Route::post('users/{user}/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('users/{user}/notifications/{type}/read-all', [NotificationController::class, 'markAllTypeRead']);
    Route::post('users/{user}/notifications/read', [NotificationController::class, 'markIDsAsRead']);
    Route::post('users/{user}/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    //Transactions
    Route::get('users/{user}/transactions', [TransactionController::class, 'userTransactions']);

    // Exports
    Route::post('exports/table', [ExportController::class, 'tableExport']);
    Route::get('exports/downloads/{file}', [ExportController::class, 'download']);

    //Tags
    Route::get('tag-groups/{tag_groups}/tags', [TagController::class, 'tagGroupTags']);

    //Statuses
    Route::get('status-groups/{status_group}/statuses', [StatusController::class, 'statusGroupStatuses']);

    //Servers
    Route::get('servers/{server}/server-metrics', [ServerMetricController::class, 'serverServerMetrics']);

    // Allowed Chat Sites
    Route::get('company/{company}/allowed-chat-sites', [AllowedChatSiteController::class, 'companyAllowedChatSites']);

    // Chats
    Route::get('/inbox/chats', [ChatController::class, 'inbox']);
    Route::get('/join/chats/{chat}', [ChatController::class, 'join']);
    Route::get('/leave/chats/{chat}', [ChatController::class, 'leave']);
    Route::post('/invite/agents', [ChatController::class, 'invite']);
    Route::get('/accept/invite/chats/{chat}', [ChatController::class, 'acceptInvite']);
    Route::get('/reject/invite/chats/{chat}', [ChatController::class, 'rejectInvite']);
    Route::get('/cobrowse/chats/{chat}', [ChatController::class, 'cobrowse']);
    Route::get('/resolve/chats/{chat}', [ChatController::class, 'resolve']);

    // Messages
    Route::get('/messages/{message}/read', [MessageController::class, 'read']);
    Route::post('/typing', [ChatController::class, 'typing']);
    Route::post('/stop/typing', [ChatController::class, 'stopTyping']);

    // Users
    Route::get('company/{company}/users', [UserController::class, 'companyUsers']);
    Route::get('chat/invite/users', [UserController::class, 'chatInviteCompanyUsers']);

    // Cobrowse
    Route::post('/signal', [ChatController::class, 'signal']);

    // Chat reviews
    Route::get('/user/{user}/chat-reviews', [ChatReviewController::class, 'userChatReviews']);

    // Action Logs
    Route::get('/chats/{chat}/action-logs', [ActionLogController::class, 'chatActionLogs']);
});


Route::prefix('v2')->group(function () {
    //Signed URLs
    Route::get('/exports/{export}/download', [ExportController::class, 'download'])->name('api.exports.download');
    Route::get('/reports/{report}/download', [ExportController::class, 'downloadReport'])->name('api.reports.download');
});

Route::post('/servers/deploy', [ServerController::class, 'deploy']);
Route::post('/chats', [ChatController::class, 'packageCreate']);
Route::get('/chats/{chat}', [ChatController::class, 'packageShow']);
Route::post('/messages', [MessageController::class, 'packageStore']);
Route::get('/messages/{message}/read', [MessageController::class, 'packageRead']);

Route::post('/typing', [ChatController::class, 'packageTyping']);
Route::post('/stop/typing', [ChatController::class, 'packageStopTyping']);

// Files
Route::post('/files', [FileController::class, 'packageStore']);

// Cobrowse
Route::get('/stop/cobrowse/chats/{chat}', [ChatController::class, 'packageStopCobrowse']);
Route::get('/cobrowse/chats/{chat}', [ChatController::class, 'packageCobrowse']);
Route::post('/signal', [ChatController::class, 'packageSignal']);

// Review
Route::post('/chat-reviews', [ChatReviewController::class, 'packageStore']);