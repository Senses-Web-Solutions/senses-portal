<?php

namespace App\Http\Controllers\Api;

use App\Actions\Subscriptions\CreateSubscription;
use App\Actions\Subscriptions\DeleteSubscription;
use App\Actions\Subscriptions\GenerateSubscriptionShowCache;
use App\Actions\Subscriptions\UpdateSubscription;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Subscriptions\CreateSubscriptionRequest;
use App\Http\Requests\Subscriptions\DeleteSubscriptionRequest;
use App\Http\Requests\Subscriptions\ListSubscriptionRequest;
use App\Http\Requests\Subscriptions\ShowSubscriptionRequest;
use App\Http\Requests\Subscriptions\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Subscription
 *
 * APIs for managing subscriptions
 */
class SubscriptionController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all subscriptions.
     * <aside><ul><li>list-subscription</li></ul></aside>
     */
    public function index(ListSubscriptionRequest $request)
    {
        return QueryBuilder::for(Subscription::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a subscription.
     * <aside><ul><li>show-subscription</li></ul></aside>
     * @urlParam subscription integer Subscription ID. Example: 1
     */
    public function show(ShowSubscriptionRequest $request, int $id, GenerateSubscriptionShowCache $generateSubscriptionShowCache)
    {
        return $generateSubscriptionShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a subscription.
     * <aside><ul><li>create-subscription</li></ul></aside>
     */
    public function store(CreateSubscriptionRequest $request, CreateSubscription $createSubscription)
    {
        return $this->respond($createSubscription->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a subscription.
     * <aside><ul><li>update-subscription</li></ul></aside>
     * @urlParam subscription integer Subscription ID. Example: 1
     */
    public function update(UpdateSubscriptionRequest $request, int $id, UpdateSubscription $updateSubscription)
    {
        return $this->respond($updateSubscription->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a subscription.
     * <aside><ul><li>delete-subscription</li></ul></aside>
     * @urlParam subscription integer Subscription ID. Example: 1
     */
    public function destroy(DeleteSubscriptionRequest $request, int $id, DeleteSubscription $deleteSubscription)
    {
        return $this->respondDeleted($deleteSubscription->execute($id));
    }

	/**
	* companySubscriptions()
	*
	* Lists subscriptions based on their company.
	* <aside><ul><li>list-subscription</li></ul></aside>
	* @urlParam companies integer[] Company IDs Example: [1,2,3]
	*/
	public function companySubscriptions(string $companyIDs)
	{
		$companyIDs = explode(',', $companyIDs);
		abort(501, 'Company subscriptions not implemented');
		//return $this->respond(QueryBuilder::for(Subscription::class)->whereIn('company_id', $companyIDs)->list());
	}

}

//Generated 04-11-2023 16:09:38
