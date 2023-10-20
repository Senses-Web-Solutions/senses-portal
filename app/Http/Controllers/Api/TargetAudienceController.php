<?php

namespace App\Http\Controllers\Api;

use App\Actions\TargetAudiences\CreateTargetAudience;
use App\Actions\TargetAudiences\DeleteTargetAudience;
use App\Actions\TargetAudiences\GenerateTargetAudienceShowCache;
use App\Actions\TargetAudiences\UpdateTargetAudience;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\TargetAudiences\CreateTargetAudienceRequest;
use App\Http\Requests\TargetAudiences\DeleteTargetAudienceRequest;
use App\Http\Requests\TargetAudiences\ListTargetAudienceRequest;
use App\Http\Requests\TargetAudiences\ShowTargetAudienceRequest;
use App\Http\Requests\TargetAudiences\UpdateTargetAudienceRequest;
use App\Models\TargetAudience;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group TargetAudience
 *
 * APIs for managing target-audiences
 */
class TargetAudienceController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all target-audiences.
     * <aside><ul><li>list-target-audience</li></ul></aside>
     */
    public function index(ListTargetAudienceRequest $request)
    {
        return QueryBuilder::for(TargetAudience::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a target-audience.
     * <aside><ul><li>show-target-audience</li></ul></aside>
     * @urlParam target-audience integer TargetAudience ID. Example: 1
     */
    public function show(ShowTargetAudienceRequest $request, int $id, GenerateTargetAudienceShowCache $generateTargetAudienceShowCache)
    {
        return $generateTargetAudienceShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a target-audience.
     * <aside><ul><li>create-target-audience</li></ul></aside>
     */
    public function store(CreateTargetAudienceRequest $request, CreateTargetAudience $createTargetAudience)
    {
        return $this->respond($createTargetAudience->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a target-audience.
     * <aside><ul><li>update-target-audience</li></ul></aside>
     * @urlParam target-audience integer TargetAudience ID. Example: 1
     */
    public function update(UpdateTargetAudienceRequest $request, int $id, UpdateTargetAudience $updateTargetAudience)
    {
        return $this->respond($updateTargetAudience->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a target-audience.
     * <aside><ul><li>delete-target-audience</li></ul></aside>
     * @urlParam target-audience integer TargetAudience ID. Example: 1
     */
    public function destroy(DeleteTargetAudienceRequest $request, int $id, DeleteTargetAudience $deleteTargetAudience)
    {
        return $this->respondDeleted($deleteTargetAudience->execute($id));
    }

	/**
	* pageTargetAudiences()
	*
	* Lists target audiences based on their page.
	* <aside><ul><li>list-target-audience</li></ul></aside>
	* @urlParam pages integer[] Page IDs Example: [1,2,3]
	*/
	public function pageTargetAudiences(string $pageIDs)
	{
		$pageIDs = explode(',', $pageIDs);
		abort(501, 'Page target audiences not implemented');
		//return $this->respond(QueryBuilder::for(TargetAudience::class)->whereHas('pages', function ($q) use ($pageIDs) {$q->whereIn('targetAudienceable_id', $pageIDs); $q->where('targetAudienceable_type', 'page')})->list());
	}

}

//Generated 09-10-2023 12:32:16
