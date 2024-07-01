<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\Status;
use App\Traits\ApiResponse;
use App\Models\StatusGroup;
use App\Support\QueryBuilder;
use App\Actions\Chats\JoinChat;
use App\Actions\Chats\LeaveChat;
use App\Actions\Chats\DeleteChat;
use App\Actions\Chats\ChatInvite;
use App\Actions\Chats\CreateChat;
use App\Actions\Chats\UpdateChat;
use App\Actions\Chats\ResolveChat;
use App\Actions\Chats\CobrowseChat;
use App\Actions\Chats\AcceptChatInvite;
use App\Actions\Chats\RejectChatInvite;
use App\Actions\Messages\TypingMessage;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Chats\SignalRequest;
use App\Http\Requests\Chats\TypingRequest;
use App\Actions\Messages\StopTypingMessage;
use App\Actions\ActionLogs\CreateActionLog;
use App\Http\Requests\Chats\ListChatRequest;
use App\Actions\Chats\GenerateChatShowCache;
use App\Http\Requests\Chats\ShowChatRequest;
use App\Http\Requests\Chats\ChatInviteRequest;
use App\Http\Requests\Chats\UpdateChatRequest;
use App\Http\Requests\Chats\CreateChatRequest;
use App\Http\Requests\Chats\DeleteChatRequest;
use App\Http\Requests\Chats\PackageTypingRequest;
use App\Http\Requests\Chats\PackageSignalRequest;
use App\Http\Requests\Chats\PackageShowChatRequest;
use App\Http\Requests\Chats\PackageCobrowseRequest;
use App\Http\Requests\Chats\PackageCreateChatRequest;

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
        return $this->respond($createChat->execute($request->all()));
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

    public function packageCreate(PackageCreateChatRequest $request, CreateChat $createChat)
    {
        $data = $request->all();

        $referrerUrl = $request->headers->get('referer');

        // Parse the referrer URL to get just the protocol and domain name
        $parsedUrl = parse_url($referrerUrl);
        $protocolAndDomain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        $data['system'] = $protocolAndDomain;

        return $this->respond($createChat->execute($data));
    }

    public function packageShow(PackageShowChatRequest $request, int $id, GenerateChatShowCache $generateChatShowCache)
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

        $chats = Chat::with([
            'messages' => function ($query) {
                $query->orderBy('id');
            },
            'messages.files',
            'agents',
            'status',
            'invitedAgents',
            'actionLogs' => function ($query) {
                $query->orderBy('logged_at', 'asc');
            },
            'actionLogs.user'
        ])
            ->where(function ($query) use ($newStatus, $assignedStatus, $userID, $companyID, $unresolvedStatus, $resolvedStatus, $missedStatus) {
                $query->where('status_id', $newStatus)
                    ->orWhere(function ($query) use ($assignedStatus, $userID) {
                $query->whereHas('agents', function ($query) use ($userID) {
                    $query->where('users.id', $userID); // Specify table name
                })
                    ->where('status_id', $assignedStatus);
                    })
                ->orWhere(function ($query) use ($companyID, $unresolvedStatus, $resolvedStatus, $missedStatus) {
                        $query->where('company_id', $companyID)
                    ->whereNotIn('status_id', [$unresolvedStatus, $resolvedStatus, $missedStatus]);
                })
                ->orWhere(function ($query) use ($userID) {
                    $query->whereHas('invitedAgents', function ($query) use ($userID) {
                        $query->where('users.id', $userID);
                    });
                    });
            })
            ->get()
            ->each(function ($chat) {
                $chat->setRelation('messages', $chat->messages->keyBy('id'));
            })
            ->append(['last_message'])
            ->sortByDesc(function ($chat) {
                return $chat->last_message ? $chat->last_message->sent_at : null;
            })
            ->values();

        $chats = $chats->keyBy('id');

        return $this->respond($chats);
    }

    public function join(Chat|int $chat)
    {
        return app(JoinChat::class)->execute($chat);
    }

    public function leave(Chat|int $chat)
    {
        return app(LeaveChat::class)->execute($chat);
    }

    public function acceptInvite(Chat|int $chat)
    {
        app(AcceptChatInvite::class)->execute($chat);
        return app(JoinChat::class)->execute($chat);
    }

    public function rejectInvite(Chat|int $chat)
    {
        app(RejectChatInvite::class)->execute($chat);
    }

    public function invite(ChatInviteRequest $request)
    {
        $data = $request->all();
        $chatID = $data['chat_id'];
        $agents = $data['agents'];
        $chat = Chat::findOrFail($chatID);

        foreach ($agents as $agent) {
            app(ChatInvite::class)->execute($chat, $agent['id']);
        }

        return $this->respond($chat);
    }

    public function resolve(Chat|int $chat)
    {
        app(CreateActionLog::class)->onQueue()->execute($chat, 'resolved', []);
        return app(ResolveChat::class)->execute($chat);
    }

    public function cobrowse(Chat|int $chat)
    {
        app(CreateActionLog::class)->onQueue()->execute($chat, 'requested-cobrowse', []);
        return app(CobrowseChat::class)->execute($chat);
    }

    public function signal(SignalRequest $request)
    {
        $payload = $request->all();
        $chatID = $payload['chat_id'];
        $data = $payload['data'];
        $fromAgent = true;

        $chat = Chat::findOrFail($chatID);

        broadcast_safely(new \App\Events\Chats\Signal($chat, $fromAgent, $data));

        return $this->respond($chat);
    }

    public function typing(TypingRequest $request, TypingMessage $typingMessage)
    {
        return $typingMessage->execute($request);
    }

    public function stopTyping(TypingRequest $request, StopTypingMessage $stopTypingMessage)
    {
        return $stopTypingMessage->execute($request);
    }

    public function packageTyping(PackageTypingRequest $request, TypingMessage $typingMessage)
    {
        return $typingMessage->execute($request);
    }

    public function packageStopTyping(PackageTypingRequest $request, StopTypingMessage $stopTypingMessage)
    {
        return $stopTypingMessage->execute($request);
    }

    public function packageCobrowse(PackageCobrowseRequest $request, int $chatID)
    {
        $chat = Chat::findOrFail($chatID);

        app(CreateActionLog::class)->onQueue()->execute($chat, 'accepted-cobrowse', []);

        broadcast_safely(new \App\Events\Chats\StartCobrowse($chatID));

        return $chat;
    }

    public function packageStopCobrowse(PackageCobrowseRequest $request, int $chatID)
    {
        $chat = Chat::findOrFail($chatID);

        app(CreateActionLog::class)->onQueue()->execute($chat, 'stopped-cobrowse', []);

        return $chat;
    }

    public function packageSignal(PackageSignalRequest $request)
    {
        $payload = $request->all();
        $chatID = $payload['chat_id'];
        $data = $payload['data'];
        $fromAgent = false;

        $chat = Chat::findOrFail($chatID);

        broadcast_safely(new \App\Events\Chats\Signal($chat, $fromAgent, $data));

        return $this->respond($chat);
    }
}

//Generated 01-11-2023 11:22:36
