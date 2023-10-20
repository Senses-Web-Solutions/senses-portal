<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Notifications\ListUserNotificationRequest;
use App\Models\Transaction;
use App\Support\QueryBuilder;
use Illuminate\Http\Request;

/**
 * @group Transaction
 *
 * APIs for managing transactions
 */
class TransactionController extends Controller
{
    /**
     * userTransactions()
    */
    public function userTransactions(ListUserNotificationRequest $request, $userID)
    {
        return QueryBuilder::for(Transaction::where('user_id', $userID))->list();
    }
}
