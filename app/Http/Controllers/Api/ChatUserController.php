<?php

namespace App\Http\Controllers\Api;

use App\Models\ChatUser;
use App\Traits\ApiResponse;
use App\Support\QueryBuilder;
use App\Http\Controllers\Api\Controller;
use App\Actions\ChatUsers\CreateChatUser;
use App\Actions\ChatUsers\UpdateChatUser;
use App\Actions\ChatUsers\DeleteChatUser;
use App\Actions\Chats\GenerateChatUserShowCache;
use App\Actions\ChatUsers\PackageFindOrCreateChatUser;
use App\Http\Requests\ChatUsers\ListChatUserRequest;
use App\Http\Requests\ChatUsers\ShowChatUserRequest;
use App\Http\Requests\ChatUsers\CreateChatUserRequest;
use App\Http\Requests\ChatUsers\DeleteChatUserRequest;
use App\Http\Requests\ChatUsers\PackageFindOrCreateChatUserRequest;
use App\Http\Requests\ChatUsers\UpdateChatUserRequest;

/**
 * @group Chat User
 *
 * APIs for managing chat users
 */
class ChatUserController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all chats.
     * <aside><ul><li>list-chat</li></ul></aside>
     */
    public function index(ListChatUserRequest $request)
    {
        return QueryBuilder::for(ChatUser::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a chat.
     * <aside><ul><li>show-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function show(ShowChatUserRequest $request, int $id, GenerateChatUserShowCache $generateChatUserShowCache)
    {
        return $generateChatUserShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a chat.
     * <aside><ul><li>create-chat</li></ul></aside>
     */
    public function store(CreateChatUserRequest $request, CreateChatUser $createChatUser)
    {
        return $this->respond($createChatUser->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a chat.
     * <aside><ul><li>update-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function update(UpdateChatUserRequest $request, int $id, UpdateChatUser $updateChatUser)
    {
        return $this->respond($updateChatUser->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a chat.
     * <aside><ul><li>delete-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function destroy(DeleteChatUserRequest $request, int $id, DeleteChatUser $deleteChatUser)
    {
        return $this->respondDeleted($deleteChatUser->execute($id));
    }

    public function packageFindOrCreateChatUser(PackageFindOrCreateChatUserRequest $request, PackageFindOrCreateChatUser $packageFindOrCreateChatUser)
    {
        $data = $request->all();
        $referrerUrl = $request->headers->get('referer');

        // Parse the referrer URL to get just the protocol and domain name
        $parsedUrl = parse_url($referrerUrl);
        $protocolAndDomain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        $data['system'] = $protocolAndDomain;

        $chatUser = $packageFindOrCreateChatUser->execute($data);

        return response()->json([
            'full_name' => $chatUser->full_name,
            'uuid' => $chatUser->uuid
        ], 200);
    }
}
