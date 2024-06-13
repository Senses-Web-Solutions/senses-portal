<?php

namespace App\Http\Controllers\Api;

use App\Actions\Files\CreateFile;
use App\Actions\Files\DeleteFile;
use App\Actions\Files\GenerateFileShowCache;
use App\Actions\Files\UpdateFile;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Files\CreateFileRequest;
use App\Http\Requests\Files\DeleteFileRequest;
use App\Http\Requests\Files\ListFileRequest;
use App\Http\Requests\Files\SensesChatCreateFileRequest;
use App\Http\Requests\Files\ShowFileRequest;
use App\Http\Requests\Files\UpdateFileRequest;
use App\Models\File;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group File
 *
 * APIs for managing files
 */
class FileController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all files.
     * <aside><ul><li>list-file</li></ul></aside>
     */
    public function index(ListFileRequest $request)
    {
        return QueryBuilder::for(File::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a file.
     * <aside><ul><li>show-file</li></ul></aside>
     * @urlParam file integer File ID. Example: 1
     */
    public function show(ShowFileRequest $request, int $id, GenerateFileShowCache $generateFileShowCache)
    {
        return $generateFileShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a file.
     * <aside><ul><li>create-file</li></ul></aside>
     */
    public function store(CreateFileRequest $request, CreateFile $createFile)
    {
        return $this->respond($createFile->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a file.
     * <aside><ul><li>update-file</li></ul></aside>
     * @urlParam file integer File ID. Example: 1
     */
    public function update(UpdateFileRequest $request, int $id, UpdateFile $updateFile)
    {
        return $this->respond($updateFile->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a file.
     * <aside><ul><li>delete-file</li></ul></aside>
     * @urlParam file integer File ID. Example: 1
     */
    public function destroy(DeleteFileRequest $request, int $id, DeleteFile $deleteFile)
    {
        return $this->respondDeleted($deleteFile->execute($id));
    }

	/**
	* eventFiles()
	*
	* Lists files based on their event.
	* <aside><ul><li>list-file</li></ul></aside>
	* @urlParam events integer[] Event IDs Example: [1,2,3]
	*/
	public function eventFiles(string $eventIDs)
	{
		$eventIDs = explode(',', $eventIDs);
		abort(501, 'Event files not implemented');
		//return $this->respond(QueryBuilder::for(File::class)->whereHas('events', function ($q) use ($eventIDs) {$q->whereIn('fileable_id', $eventIDs); $q->where('fileable_type', 'event')})->list());
	}

	/**
	* serviceFiles()
	*
	* Lists files based on their service.
	* <aside><ul><li>list-file</li></ul></aside>
	* @urlParam services integer[] Service IDs Example: [1,2,3]
	*/
	public function serviceFiles(string $serviceIDs)
	{
		$serviceIDs = explode(',', $serviceIDs);
		abort(501, 'Service files not implemented');
		//return $this->respond(QueryBuilder::for(File::class)->whereHas('services', function ($q) use ($serviceIDs) {$q->whereIn('fileable_id', $serviceIDs); $q->where('fileable_type', 'service')})->list());
	}

    /**
     * store()
     *
     * Creates, saves and returns a file.
     * <aside><ul><li>create-file</li></ul></aside>
     */
    public function sensesChatStore(SensesChatCreateFileRequest $request, CreateFile $createFile)
    {
        return $this->respond($createFile->execute($request->all()));
    }
}

//Generated 09-10-2023 13:46:51
