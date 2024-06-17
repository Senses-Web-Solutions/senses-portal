<?php

namespace App\Http\Controllers\Api;

use App\Actions\Chats\AcceptChat;
use App\Actions\Chats\CreateChat;
use App\Actions\Chats\DeleteChat;
use App\Actions\Chats\GenerateChatShowCache;
use App\Actions\Chats\UpdateChat;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Chats\CreateChatRequest;
use App\Http\Requests\Chats\DeleteChatRequest;
use App\Http\Requests\Chats\ListChatRequest;
use App\Http\Requests\Chats\SensesChatTypingRequest;
use App\Http\Requests\Chats\ShowChatRequest;
use App\Http\Requests\Chats\ShowSensesChatRequest;
use App\Http\Requests\Chats\StartChatRequest;
use App\Http\Requests\Chats\TypingRequest;
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

    public function sensesChatStart(StartChatRequest $request, CreateChat $createChat)
    {
        $data = $request->all();

        $referrerUrl = $request->headers->get('referer');

        // Parse the referrer URL to get just the protocol and domain name
        $parsedUrl = parse_url($referrerUrl);
        $protocolAndDomain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        $data['system'] = $protocolAndDomain;

        return $this->respond($createChat->execute($data));
    }

    public function sensesChatFetch(ShowSensesChatRequest $request, int $id, GenerateChatShowCache $generateChatShowCache)
    {
        return $generateChatShowCache->execute($id);
    }

    /**
     * inbox()
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

        $chats = Chat::with([
            'messages' => function ($query) {
                $query->orderBy('id');
            },
            'messages.files',
            'agents',
            'status'
        ])
            ->where(function ($query) use ($newStatus, $assignedStatus, $userID, $agentInvitedStatus, $companyID, $unresolvedStatus, $resolvedStatus, $missedStatus) {
                $query->where('status_id', $newStatus)
                    ->orWhere(function ($query) use ($assignedStatus, $userID) {
                $query->whereHas('agents', function ($query) use ($userID) {
                    $query->where('users.id', $userID); // Specify table name
                })
                    ->where('status_id', $assignedStatus);
                    })
                    ->orWhere(function ($query) use ($agentInvitedStatus, $companyID, $userID) {
                        $query->where('status_id', $agentInvitedStatus)
                            ->where('company_id', $companyID)
                    ->whereHas('agents', function ($query) use ($userID) {
                        $query->where('users.id', $userID); // Specify table name
                    });
                    })
                    ->orWhere(function ($query) use ($companyID, $unresolvedStatus, $resolvedStatus, $missedStatus, $agentInvitedStatus) {
                        $query->where('company_id', $companyID)
                            ->whereNotIn('status_id', [$unresolvedStatus, $resolvedStatus, $missedStatus, $agentInvitedStatus]);
                    });
            })
            ->get()
            ->each(function ($chat) {
                $chat->setRelation('messages', $chat->messages->keyBy('id'));
            })
            ->append(['last_message', 'unread_messages_count',])
            ->sortByDesc(function ($chat) {
                return $chat->last_message ? $chat->last_message->sent_at : null;
            })
            ->values();

        $chats = $chats->keyBy('id');

        return $this->respond($chats);
    }

    public function accept(Chat|int $chat)
    {

        if (is_int($chat)) {
            $chat = Chat::findOrFail($chat);
        }

        return app(AcceptChat::class)->execute($chat);
    }

    public function typing(TypingRequest $request)
    {
        $data = $request->all();
        $chatID = $data['chat_id'];
        $name = $data['name'];
        $fromAgent = $data['from_agent'];
        broadcast_safely(new \App\Events\Chats\Typing($chatID, $name, $fromAgent));

        return response()->json(['chat_id' => $chatID, 'name' => $name, 'from_agent' => $fromAgent]);
    }

    public function stopTyping(TypingRequest $request)
    {
        $data = $request->all();
        $chatID = $data['chat_id'];
        $name = $data['name'];
        $fromAgent = $data['from_agent'];
        broadcast_safely(new \App\Events\Chats\StopTyping($chatID, $name, $fromAgent));

        return response()->json(['chat_id' => $chatID, 'name' => $name, 'from_agent' => $fromAgent]);
    }

    public function sensesChatTyping(SensesChatTypingRequest $request)
    {
        $data = $request->all();
        $chatID = $data['chat_id'];
        $name = $data['name'];
        $fromAgent = $data['from_agent'];
        broadcast_safely(new \App\Events\Chats\Typing($chatID, $name, $fromAgent));

        return response()->json(['chat_id' => $chatID, 'name' => $name, 'from_agent' => $fromAgent]);
    }

    public function sensesChatStopTyping(SensesChatTypingRequest $request)
    {
        $data = $request->all();
        $chatID = $data['chat_id'];
        $name = $data['name'];
        $fromAgent = $data['from_agent'];
        broadcast_safely(new \App\Events\Chats\StopTyping($chatID, $name, $fromAgent));

        return response()->json(['chat_id' => $chatID, 'name' => $name, 'from_agent' => $fromAgent]);
    }
}

//Generated 01-11-2023 11:22:36
