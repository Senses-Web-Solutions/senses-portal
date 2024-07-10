<?php

namespace App\Http\Controllers\Api;

use App\Models\CannedMessage;
use App\Traits\ApiResponse;
use App\Support\QueryBuilder;
use App\Http\Controllers\Api\Controller;
use App\Actions\CannedMessages\CreateCannedMessage;
use App\Actions\CannedMessages\UpdateCannedMessage;
use App\Actions\CannedMessages\DeleteCannedMessage;
use App\Actions\CannedMessages\GenerateCannedMessageShowCache;
use App\Http\Requests\CannedMessages\ListCannedMessageRequest;
use App\Http\Requests\CannedMessages\ShowCannedMessageRequest;
use App\Http\Requests\CannedMessages\CreateCannedMessageRequest;
use App\Http\Requests\CannedMessages\DeleteCannedMessageRequest;
use App\Http\Requests\CannedMessages\UpdateCannedMessageRequest;

/**
 * @group Chat User
 *
 * APIs for managing chat users
 */
class CannedMessageController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all chats.
     * <aside><ul><li>list-chat</li></ul></aside>
     */
    public function index(ListCannedMessageRequest $request)
    {
        return QueryBuilder::for(CannedMessage::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a chat.
     * <aside><ul><li>show-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function show(ShowCannedMessageRequest $request, int $id, GenerateCannedMessageShowCache $generateCannedMessageShowCache)
    {
        return $generateCannedMessageShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a chat.
     * <aside><ul><li>create-chat</li></ul></aside>
     */
    public function store(CreateCannedMessageRequest $request, CreateCannedMessage $createCannedMessage)
    {
        return $this->respond($createCannedMessage->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a chat.
     * <aside><ul><li>update-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function update(UpdateCannedMessageRequest $request, int $id, UpdateCannedMessage $updateCannedMessage)
    {
        return $this->respond($updateCannedMessage->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a chat.
     * <aside><ul><li>delete-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function destroy(DeleteCannedMessageRequest $request, int $id, DeleteCannedMessage $deleteCannedMessage)
    {
        return $this->respondDeleted($deleteCannedMessage->execute($id));
    }

    // public function listCompanyCannedMessages(ListCompanyCannedMessageRequest $request, int $companyID)
    // {
    //     return QueryBuilder::for(CannedMessage::class)
    //         ->where('company_id', $companyID)
    //         ->list();
    // }

    public function userCannedMessages(ListCannedMessageRequest $request, int $userID)
    {
        return QueryBuilder::for(CannedMessage::class)
            ->where('user_id', $userID)
            ->list();
    }
}
