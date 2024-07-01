<?php

use App\Actions\ChatReviews\CreateChatReview;
use App\Actions\ChatReviews\DeleteChatReview;
use App\Actions\ChatReviews\GenerateChatReviewShowCache;
use App\Actions\ChatReviews\UpdateChatReview;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\ChatReviews\CreateChatReviewRequest;
use App\Http\Requests\ChatReviews\PackageCreateChatReviewRequest;
use App\Http\Requests\ChatReviews\UpdateChatReviewRequest;
use App\Http\Requests\Chats\DeleteChatReviewRequest;
use App\Http\Requests\Chats\ListChatReviewRequest;
use App\Http\Requests\Chats\ShowChatReviewRequest;
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
            ->whereHas('chat.historicalAgents', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->list();
    }

    public function packageStore(PackageCreateChatReviewRequest $request, CreateChatReview $createChatReview)
    {
        return $this->respond($createChatReview->execute($request->all()));
    }
}
