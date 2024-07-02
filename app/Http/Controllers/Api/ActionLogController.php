<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponse;
use App\Support\QueryBuilder;
use App\Http\Controllers\Api\Controller;
use App\Models\ActionLog;
use App\Models\Chat;

/**
 * @group Ability
 *
 * APIs for managing abilities
 */
class ActionLogController extends Controller
{

    use ApiResponse;

    /**
     * chatActionLogs()
     *
     * Reads and returns a collection of all chat action logs
     * <aside><ul><li>show-chat</li></ul></aside>
     */
    public function chatActionLogs($chatID)
    {
        return QueryBuilder::for(
            ActionLog::whereMorphRelation('loggable', [Chat::class], 'id', '=', $chatID)
        )->orderBy('id', 'asc')->list();
    }
}
