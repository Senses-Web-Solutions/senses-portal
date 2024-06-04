<?php

namespace App\Http\Controllers\Api;

use App\Actions\Chats\CreateChat;
use App\Actions\Chats\DeleteChat;
use App\Actions\Chats\GenerateChatShowCache;
use App\Actions\Chats\UpdateChat;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Chats\CreateChatRequest;
use App\Http\Requests\Chats\DeleteChatRequest;
use App\Http\Requests\Chats\ListChatRequest;
use App\Http\Requests\Chats\ShowChatRequest;
use App\Http\Requests\Chats\UpdateChatRequest;
use App\Models\Chat;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;

/**
 * @group Chat
 *
 * APIs for managing chats
 */
class ChatController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all chats.
     * <aside><ul><li>list-chat</li></ul></aside>
     */
    public function index(ListChatRequest $request)
    {
        return QueryBuilder::for(Chat::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a chat.
     * <aside><ul><li>show-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function show(ShowChatRequest $request, int $id, GenerateChatShowCache $generateChatShowCache)
    {
        return $generateChatShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a chat.
     * <aside><ul><li>create-chat</li></ul></aside>
     */
    public function store(CreateChatRequest $request, CreateChat $createChat)
    {
        $data = $request->all();

        return $this->respond($createChat->execute($data));
    }

    /**
     * update()
     *
     * Updates, saves and returns a chat.
     * <aside><ul><li>update-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function update(UpdateChatRequest $request, int $id, UpdateChat $updateChat)
    {
        return $this->respond($updateChat->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a chat.
     * <aside><ul><li>delete-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function destroy(DeleteChatRequest $request, int $id, DeleteChat $deleteChat)
    {
        return $this->respondDeleted($deleteChat->execute($id));
    }

    /**
     * companyChats()
     *
     * Lists server metrics based on their company.
     * <aside><ul><li>list-chat</li></ul></aside>
     * @urlParam companies integer[] Company IDs Example: [1,2,3]
     */
    public function companyChats(string $companyIDs)
    {
        $companyIDs = explode(',', $companyIDs);
        abort(501, 'Company server metrics not implemented');
        //return $this->respond(QueryBuilder::for(Chat::class)->whereIn('company_id', $companyIDs)->list());
    }

    /**
     * serverChats()
     *
     * Lists server metrics based on their server.
     * <aside><ul><li>list-chat</li></ul></aside>
     * @urlParam servers integer[] Server IDs Example: [1,2,3]
     */
    public function serverChats(string $serverIDs)
    {
        $serverIDs = explode(',', $serverIDs);
        abort(501, 'Server server metrics not implemented');
        // return $this->respond(QueryBuilder::for(Chat::class)->whereIn('server_id', $serverIDs)->list());
    }
}

//Generated 01-11-2023 11:22:36
