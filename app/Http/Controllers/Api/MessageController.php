<?php

namespace App\Http\Controllers\Api;

use App\Actions\ActionLogs\CreateActionLog;
use App\Actions\Chats\UpdateChatCurrentPage;
use App\Actions\Messages\CreateMessage;
use App\Actions\Messages\DeleteMessage;
use App\Actions\Messages\GenerateMessageShowCache;
use App\Actions\Messages\ReadMessage;
use App\Actions\Messages\UpdateMessage;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Messages\CreateMessageRequest;
use App\Http\Requests\Messages\DeleteMessageRequest;
use App\Http\Requests\Messages\ListMessageRequest;
use App\Http\Requests\Messages\ReadMessageRequest;
use App\Http\Requests\Messages\packageCreateMessageRequest;
use App\Http\Requests\Messages\packageReadMessageRequest;
use App\Http\Requests\Messages\ShowMessageRequest;
use App\Http\Requests\Messages\UpdateMessageRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;

/**
 * @group Message
 *
 * APIs for managing messages
 */
class MessageController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all messages.
     * <aside><ul><li>list-message</li></ul></aside>
     */
    public function index(ListMessageRequest $request)
    {
        return QueryBuilder::for(Message::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a message.
     * <aside><ul><li>show-message</li></ul></aside>
     * @urlParam message integer Message ID. Example: 1
     */
    public function show(ShowMessageRequest $request, int $id, GenerateMessageShowCache $generateMessageShowCache)
    {
        return $generateMessageShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a message.
     * <aside><ul><li>create-message</li></ul></aside>
     */
    public function store(CreateMessageRequest $request, CreateMessage $createMessage)
    {
        $data = $request->all();

        return $this->respond($createMessage->execute($data));
    }

    /**
     * update()
     *
     * Updates, saves and returns a message.
     * <aside><ul><li>update-message</li></ul></aside>
     * @urlParam message integer Message ID. Example: 1
     */
    public function update(UpdateMessageRequest $request, int $id, UpdateMessage $updateMessage)
    {
        return $this->respond($updateMessage->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a message.
     * <aside><ul><li>delete-message</li></ul></aside>
     * @urlParam message integer Message ID. Example: 1
     */
    public function destroy(DeleteMessageRequest $request, int $id, DeleteMessage $deleteMessage)
    {
        return $this->respondDeleted($deleteMessage->execute($id));
    }

    public function read(ReadMessageRequest $request, int $id, ReadMessage $readMessage)
    {
        $userID = getCurrentUser()->id;
        return $this->respond($readMessage->execute($id, $userID));
    }

    public function packageStore(packageCreateMessageRequest $request, CreateMessage $createMessage)
    {
        $data = $request->all();

        $currentPage = $data['current_page'] ?? null;

        app(UpdateChatCurrentPage::class)->onQueue()->execute($data['chat_id'], $currentPage);

        return $this->respond($createMessage->execute($data));
    }

    public function packageRead(packageReadMessageRequest $request, int $id, ReadMessage $readMessage)
    {
        // Senses system user id
        $userID = getSensesSystemUser()->id;
        return $this->respond($readMessage->execute($id, $userID));
    }
}

//Generated 01-11-2023 11:22:36
