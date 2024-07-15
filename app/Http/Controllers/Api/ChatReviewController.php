<?php

namespace App\Http\Controllers\Api;

use App\Actions\ChatReviews\CreateChatReview;
use App\Actions\ChatReviews\DeleteChatReview;
use App\Actions\ChatReviews\GenerateChatReviewShowCache;
use App\Actions\ChatReviews\UpdateChatReview;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\ChatReviews\CreateChatReviewRequest;
use App\Http\Requests\ChatReviews\PackageCreateChatReviewRequest;
use App\Http\Requests\ChatReviews\UpdateChatReviewRequest;
use App\Http\Requests\ChatReviews\DeleteChatReviewRequest;
use App\Http\Requests\ChatReviews\ListChatReviewRequest;
use App\Http\Requests\ChatReviews\ShowChatReviewRequest;
use App\Models\ChatReview;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;

/**
 * @group Chat
 *
 * APIs for managing chats
 */
class ChatReviewController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all chat reviews.
     * <aside><ul><li>list-chat</li></ul></aside>
     */
    public function index(ListChatReviewRequest $request)
    {
        return QueryBuilder::for(ChatReview::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a chat review.
     * <aside><ul><li>show-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function show(ShowChatReviewRequest $request, int $id, GenerateChatReviewShowCache $generateChatShowCache)
    {
        return $generateChatShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a chat.
     * <aside><ul><li>create-chat</li></ul></aside>
     */
    public function store(CreateChatReviewRequest $request, CreateChatReview $createChatReview)
    {
        return $this->respond($createChatReview->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a chat.
     * <aside><ul><li>update-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function update(UpdateChatReviewRequest $request, int $id, UpdateChatReview $updateChat)
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
    public function destroy(DeleteChatReviewRequest $request, int $id, DeleteChatReview $deleteChat)
    {
        return $this->respondDeleted($deleteChat->execute($id));
    }

    public function userChatReviews(ListChatReviewRequest $request, int $userId)
    {
        return QueryBuilder::for(ChatReview::class)
            ->whereHas('chat.historicalAgents', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->list();
    }

    public function userChatReviewStats(ListChatReviewRequest $request, int $userId)
    {
        $chatReviews = ChatReview::whereHas('chat.historicalAgents', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        $data = [
            'overall' => round($chatReviews->avg('overall'), 1),
            'knowledge' => round($chatReviews->avg('knowledge'), 1),
            'friendliness' => round($chatReviews->avg('friendliness'), 1),
            'responsiveness' => round($chatReviews->avg('responsiveness'), 1),
        ];

        return $this->respond($data);
    }

    public function chatUserChatReviews(ListChatReviewRequest $request, int $chatUserId)
    {
        return QueryBuilder::for(ChatReview::class)
            ->where('chat_user_id', $chatUserId)
            ->list();
    }

    public function chatUserChatReviewStats(ListChatReviewRequest $request, int $chatUserId)
    {
        $chatReviews = ChatReview::where('chat_user_id', $chatUserId)->get();

        $data = [
            'overall' => round($chatReviews->avg('overall'), 1),
            'knowledge' => round($chatReviews->avg('knowledge'), 1),
            'friendliness' => round($chatReviews->avg('friendliness'), 1),
            'responsiveness' => round($chatReviews->avg('responsiveness'), 1),
        ];

        return $this->respond($data);
    }

    public function packageStore(PackageCreateChatReviewRequest $request, CreateChatReview $createChatReview)
    {
        return $this->respond($createChatReview->execute($request->all()));
    }
}
