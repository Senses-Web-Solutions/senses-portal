<?php

namespace App\Http\Controllers\Api;

use App\Actions\AllowedChatSites\CreateAllowedChatSite;
use App\Actions\AllowedChatSites\UpdateAllowedChatSite;
use App\Actions\AllowedChatSites\DeleteAllowedChatSite;
use App\Actions\AllowedChatSites\GenerateAllowedChatSiteShowCache;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\AllowedChatSites\CreateAllowedChatSiteRequest;
use App\Http\Requests\AllowedChatSites\DeleteAllowedChatSiteRequest;
use App\Http\Requests\AllowedChatSites\ListAllowedChatSiteRequest;
use App\Http\Requests\AllowedChatSites\ShowAllowedChatSiteRequest;
use App\Http\Requests\AllowedChatSites\UpdateAllowedChatSiteRequest;
use App\Models\AllowedChatSite;
use App\Models\Chat;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;

/**
 * @group Chat
 *
 * APIs for managing chats
 */
class AllowedChatSiteController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all chats.
     * <aside><ul><li>list-chat</li></ul></aside>
     */
    public function index(ListAllowedChatSiteRequest $request)
    {
        return QueryBuilder::for(AllowedChatSite::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a chat.
     * <aside><ul><li>show-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function show(ShowAllowedChatSiteRequest $request, int $id, GenerateAllowedChatSiteShowCache $generateChatShowCache)
    {
        return $generateChatShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a chat.
     * <aside><ul><li>create-chat</li></ul></aside>
     */
    public function store(CreateAllowedChatSiteRequest $request, CreateAllowedChatSite $createAllowedChatSite)
    {
        $data = $request->all();

        return $this->respond($createAllowedChatSite->execute($data));
    }

    /**
     * update()
     *
     * Updates, saves and returns a chat.
     * <aside><ul><li>update-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function update(UpdateAllowedChatSiteRequest $request, int $id, UpdateAllowedChatSite $updateAllowedChatSite)
    {
        return $this->respond($updateAllowedChatSite->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a chat.
     * <aside><ul><li>delete-chat</li></ul></aside>
     * @urlParam chat integer Chat ID. Example: 1
     */
    public function destroy(DeleteAllowedChatSiteRequest $request, int $id, DeleteAllowedChatSite $deleteAllowedChatSite)
    {
        return $this->respondDeleted($deleteAllowedChatSite->execute($id));
    }

    public function companyAllowedChatSites(ListAllowedChatSiteRequest $request, int $id)
    {
        return QueryBuilder::for(AllowedChatSite::class)
            ->where('company_id', $id)
            ->list();
    }
}

//Generated 01-11-2023 11:22:36
