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
use App\Models\Status;
use App\Models\StatusGroup;
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
     * inboxChats()
     *
     * Fetches your assigned chats and incoming chats.
     * <aside><ul><li>list-chat</li></ul></aside>
     */
    public function inbox(ListChatRequest $request)
    {
        $userID = auth()->user()->id;
        $companyID = auth()->user()->company_id;

        $statusGroupID = StatusGroup::where('slug', 'chat')->pluck('id')->first();

        $statusIDs = Status::whereHas('statusGroups', function ($query) use ($statusGroupID) {
            $query->where('status_group_id', $statusGroupID);
        })->pluck('id', 'slug');

        $newStatus = $statusIDs['new'];
        $unassignedStatus = $statusIDs['unassigned'];
        $assignedStatus = $statusIDs['assigned'];
        $resolvedStatus = $statusIDs['resolved'];
        $unresolvedStatus = $statusIDs['unresolved'];
        $missedStatus = $statusIDs['missed'];
        $agentInvitedStatus = $statusIDs['agent-invited'];

        $chats = Chat::with(['messages', 'user', 'status'])->where(function ($query) use ($newStatus, $assignedStatus, $userID, $agentInvitedStatus, $companyID, $unresolvedStatus, $resolvedStatus, $missedStatus) {
            $query->where('status_id', $newStatus)
                ->orWhere(function ($query) use ($assignedStatus, $userID) {
                    $query->where('status_id', $assignedStatus)
                        ->where('user_id', $userID);
                })
                ->orWhere(function ($query) use ($agentInvitedStatus, $companyID, $userID) {
                    $query->where('status_id', $agentInvitedStatus)
                        ->where('company_id', $companyID)
                        ->where('invited_user_id', $userID);
                })
                ->orWhere(function ($query) use ($companyID, $unresolvedStatus, $resolvedStatus, $missedStatus, $agentInvitedStatus) {
                    $query->where('company_id', $companyID)
                        ->whereNotIn('status_id', [$unresolvedStatus, $resolvedStatus, $missedStatus, $agentInvitedStatus]);
                });
        })
            ->get()
            ->append(['last_message', 'unread_messages_count',])
            ->sortByDesc(function ($chat) {
                return $chat->last_message ? $chat->last_message->sent_at : null;
            })
            ->values();;

        // $data = [
        //     'all' => $chats,
        //     'new' => $chats->where('status_id', $newStatus),
        //     'assigned' => $chats->where('status_id', $assignedStatus)->where('user_id', $userID),
        //     'invited' => $chats->where('status_id', $agentInvitedStatus)->where('invited_user_id', $userID),
        // ];

        $chats->keyBy('id');

        return $this->respond($chats);
    }

    public function accept(Chat|int $chat)
    {

        if (is_int($chat)) {
            $chat = Chat::findOrFail($chat);
        }

        $assignedStatus = Status::where('slug', 'assigned')->first();
        $chat->status()->associate($assignedStatus);
        $userID = auth()->user()->id;
        $chat->user()->associate($userID);

        $chat->save();

        $chat->load('user');

        return $this->respond($chat);
    }
}

//Generated 01-11-2023 11:22:36
