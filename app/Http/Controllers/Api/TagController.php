<?php

namespace App\Http\Controllers\Api;

use App\Actions\Tags\CreateTag;
use App\Actions\Tags\DeleteTag;
use App\Actions\Tags\GenerateTagShowCache;
use App\Actions\Tags\UpdateTag;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Tags\CreateTagRequest;
use App\Http\Requests\Tags\DeleteTagRequest;
use App\Http\Requests\Tags\ListTagRequest;
use App\Http\Requests\Tags\ShowTagRequest;
use App\Http\Requests\Tags\UpdateTagRequest;
use App\Models\Tag;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Tag
 *
 * APIs for managing tags
 */
class TagController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all tags.
     * <aside><ul><li>list-tag</li></ul></aside>
     */
    public function index(ListTagRequest $request)
    {
        return QueryBuilder::for(Tag::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a tag.
     * <aside><ul><li>show-tag</li></ul></aside>
     * @urlParam tag integer Tag ID. Example: 1
     */
    public function show(ShowTagRequest $request, int $id, GenerateTagShowCache $generateTagShowCache)
    {
        return $generateTagShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a tag.
     * <aside><ul><li>create-tag</li></ul></aside>
     */
    public function store(CreateTagRequest $request, CreateTag $createTag)
    {
        return $this->respond($createTag->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a tag.
     * <aside><ul><li>update-tag</li></ul></aside>
     * @urlParam tag integer Tag ID. Example: 1
     */
    public function update(UpdateTagRequest $request, int $id, UpdateTag $updateTag)
    {
        return $this->respond($updateTag->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a tag.
     * <aside><ul><li>delete-tag</li></ul></aside>
     * @urlParam tag integer Tag ID. Example: 1
     */
    public function destroy(DeleteTagRequest $request, int $id, DeleteTag $deleteTag)
    {
        return $this->respondDeleted($deleteTag->execute($id));
    }

	/**
	* serviceTags()
	*
	* Lists tags based on their service.
	* <aside><ul><li>list-tag</li></ul></aside>
	* @urlParam services integer[] Service IDs Example: [1,2,3]
	*/
	public function serviceTags(string $serviceIDs)
	{
		$serviceIDs = explode(',', $serviceIDs);
		abort(501, 'Service tags not implemented');
		//return $this->respond(QueryBuilder::for(Tag::class)->whereHas('services', function ($q) use ($serviceIDs) {$q->whereIn('taggable_id', $serviceIDs); $q->where('taggable_type', 'service')})->list());
	}

	/**
	* eventTags()
	*
	* Lists tags based on their event.
	* <aside><ul><li>list-tag</li></ul></aside>
	* @urlParam events integer[] Event IDs Example: [1,2,3]
	*/
	public function eventTags(string $eventIDs)
	{
		$eventIDs = explode(',', $eventIDs);
		abort(501, 'Event tags not implemented');
		//return $this->respond(QueryBuilder::for(Tag::class)->whereHas('events', function ($q) use ($eventIDs) {$q->whereIn('taggable_id', $eventIDs); $q->where('taggable_type', 'event')})->list());
	}

	/**
	* venueTags()
	*
	* Lists tags based on their venue.
	* <aside><ul><li>list-tag</li></ul></aside>
	* @urlParam venues integer[] Venue IDs Example: [1,2,3]
	*/
	public function venueTags(string $venueIDs)
	{
		$venueIDs = explode(',', $venueIDs);
		abort(501, 'Venue tags not implemented');
		//return $this->respond(QueryBuilder::for(Tag::class)->whereHas('venues', function ($q) use ($venueIDs) {$q->whereIn('taggable_id', $venueIDs); $q->where('taggable_type', 'venue')})->list());
	}

	/**
	* fileTags()
	*
	* Lists tags based on their file.
	* <aside><ul><li>list-tag</li></ul></aside>
	* @urlParam files integer[] File IDs Example: [1,2,3]
	*/
	public function fileTags(string $fileIDs)
	{
		$fileIDs = explode(',', $fileIDs);
		abort(501, 'File tags not implemented');
		//return $this->respond(QueryBuilder::for(Tag::class)->whereHas('files', function ($q) use ($fileIDs) {$q->whereIn('taggable_id', $fileIDs); $q->where('taggable_type', 'file')})->list());
	}

	/**
	* linkTags()
	*
	* Lists tags based on their link.
	* <aside><ul><li>list-tag</li></ul></aside>
	* @urlParam links integer[] Link IDs Example: [1,2,3]
	*/
	public function linkTags(string $linkIDs)
	{
		$linkIDs = explode(',', $linkIDs);
		abort(501, 'Link tags not implemented');
		//return $this->respond(QueryBuilder::for(Tag::class)->whereHas('links', function ($q) use ($linkIDs) {$q->whereIn('taggable_id', $linkIDs); $q->where('taggable_type', 'link')})->list());
	}

	/**
	* tagGroupTags()
	*
	* Lists tags based on their tag group.
	* <aside><ul><li>list-tag</li></ul></aside>
	* @urlParam tag-groups integer[] Tag Group IDs Example: [1,2,3]
	*/
	public function tagGroupTags(string $tagGroupIDs)
	{
		$tagGroupIDs = explode(',', $tagGroupIDs);
		return $this->respond(QueryBuilder::for(Tag::class)->whereHas('tagGroups', function ($q) use ($tagGroupIDs) {$q->whereIn('tag_groups.id', $tagGroupIDs);})->list());
	}

}

//Generated 09-10-2023 10:18:19
