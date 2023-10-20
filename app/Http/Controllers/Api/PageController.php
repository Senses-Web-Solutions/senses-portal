<?php

namespace App\Http\Controllers\Api;

use App\Actions\Pages\CreatePage;
use App\Actions\Pages\DeletePage;
use App\Actions\Pages\GeneratePageShowCache;
use App\Actions\Pages\UpdatePage;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Pages\CreatePageRequest;
use App\Http\Requests\Pages\DeletePageRequest;
use App\Http\Requests\Pages\ListPageRequest;
use App\Http\Requests\Pages\ShowPageRequest;
use App\Http\Requests\Pages\UpdatePageRequest;
use App\Models\Page;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Page
 *
 * APIs for managing pages
 */
class PageController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all pages.
     * <aside><ul><li>list-page</li></ul></aside>
     */
    public function index(ListPageRequest $request)
    {
        return QueryBuilder::for(Page::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a page.
     * <aside><ul><li>show-page</li></ul></aside>
     * @urlParam page integer Page ID. Example: 1
     */
    public function show(ShowPageRequest $request, int $id, GeneratePageShowCache $generatePageShowCache)
    {
        return $generatePageShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a page.
     * <aside><ul><li>create-page</li></ul></aside>
     */
    public function store(CreatePageRequest $request, CreatePage $createPage)
    {
        return $this->respond($createPage->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a page.
     * <aside><ul><li>update-page</li></ul></aside>
     * @urlParam page integer Page ID. Example: 1
     */
    public function update(UpdatePageRequest $request, int $id, UpdatePage $updatePage)
    {
        return $this->respond($updatePage->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a page.
     * <aside><ul><li>delete-page</li></ul></aside>
     * @urlParam page integer Page ID. Example: 1
     */
    public function destroy(DeletePageRequest $request, int $id, DeletePage $deletePage)
    {
        return $this->respondDeleted($deletePage->execute($id));
    }

	/**
	* keywordPages()
	*
	* Lists pages based on their keyword.
	* <aside><ul><li>list-page</li></ul></aside>
	* @urlParam keywords integer[] Keyword IDs Example: [1,2,3]
	*/
	public function keywordPages(string $keywordIDs)
	{
		$keywordIDs = explode(',', $keywordIDs);
		return $this->respond(QueryBuilder::for(Page::class)->whereHas('keywords', function ($q) use ($keywordIDs) {
            $q->whereIn('keywordable_id', $keywordIDs); $q->where('keywordable_type', 'keyword');
        })->list()
        );
	}

}

//Generated 10-10-2023 14:43:35
