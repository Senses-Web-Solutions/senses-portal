<?php

namespace App\Http\Controllers\Api;

use App\Actions\ServerMetrics\CreateServerMetric;
use App\Actions\Servers\AuthenticateServer;
use App\Actions\Servers\CreateServer;
use App\Actions\Servers\DeleteServer;
use App\Actions\Servers\GenerateServerShowCache;
use App\Actions\Servers\UpdateServer;
use App\Actions\Servers\ValidateServer;
use App\Events\Servers\ServerDeployed;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\BlockGroups\CreateServerRequest as BlockGroupsCreateServerRequest;
use App\Http\Requests\ServerMetrics\CreateServerMetricRequest;
use App\Http\Requests\Servers\CreateServerRequest;
use App\Http\Requests\Servers\DeleteServerRequest;
use App\Http\Requests\Servers\ListServerRequest;
use App\Http\Requests\Servers\ShowServerRequest;
use App\Http\Requests\Servers\UpdateServerRequest;
use App\Http\Requests\Servers\ValidateServerRequest;
use App\Models\Server;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Server
 *
 * APIs for managing tag-groups
 */
class ServerController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all tag-groups.
     * <aside><ul><li>list-tag-group</li></ul></aside>
     */
    public function index(ListServerRequest $request)
    {
        return QueryBuilder::for(Server::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a tag-group.
     * <aside><ul><li>show-tag-group</li></ul></aside>
     * @urlParam tag-group integer Server ID. Example: 1
     */
    public function show(ShowServerRequest $request, int $id, GenerateServerShowCache $generateServerShowCache)
    {
        return $generateServerShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a tag-group.
     * <aside><ul><li>create-tag-group</li></ul></aside>
     */
    public function store(CreateServerRequest $request, CreateServer $createServer)
    {
        $data = $request->all();
        $data['company_id'] = $request->user()->company_id;

        return $this->respond($createServer->execute($data));
    }

    /**
     * update()
     *
     * Updates, saves and returns a tag-group.
     * <aside><ul><li>update-tag-group</li></ul></aside>
     * @urlParam tag-group integer Server ID. Example: 1
     */
    public function update(UpdateServerRequest $request, int $id, UpdateServer $updateServer)
    {
        return $this->respond($updateServer->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a tag-group.
     * <aside><ul><li>delete-tag-group</li></ul></aside>
     * @urlParam tag-group integer Server ID. Example: 1
     */
    public function destroy(DeleteServerRequest $request, int $id, DeleteServer $deleteServer)
    {
        return $this->respondDeleted($deleteServer->execute($id));
    }


    /**
     * validate()
     *
     * Deletes a tag-group.
     * <aside><ul><li>delete-tag-group</li></ul></aside>
     * @urlParam tag-group integer Server ID. Example: 1
     */
    public function validateServer(ValidateServerRequest $request, ValidateServer $validateServer)
    {
        $data = $request->all();
        $data['server_id'] = $request->user()->id; // user() is actually the Server model that has the API Key

        return $this->respond($validateServer->execute($data));
    }

    public function deploy(ValidateServerRequest $request)
    {
        $server = app(AuthenticateServer::class)->execute($request->getPassword());

        if (!$server) {
            logger("Deploy detected with invalid token: " . $request->getPassword());
            return;
        }

        $data = $request->all();

        broadcast_safely(new ServerDeployed($server, json_decode($data['payload'])));
    }
}

//Generated 09-10-2023 10:26:55
