<?php

namespace App\Http\Controllers\Api;

use App\Actions\Users\CreateUser;
use App\Actions\Users\DeleteUser;
use App\Actions\Users\GenerateUserShowCache;
use App\Actions\Users\UpdateUser;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\DeleteUserRequest;
use App\Http\Requests\Users\ListChatInviteUserRequest;
use App\Http\Requests\Users\ListUserRequest;
use App\Http\Requests\Users\ShowUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group User
 *
 * APIs for managing users
 */
class UserController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all users.
     * <aside><ul><li>list-user</li></ul></aside>
     */
    public function index(ListUserRequest $request)
    {
        return QueryBuilder::for(User::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a user.
     * <aside><ul><li>show-user</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     */
    public function show(ShowUserRequest $request, int $id, GenerateUserShowCache $generateUserShowCache)
    {
        return $generateUserShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a user.
     * <aside><ul><li>create-user</li></ul></aside>
     */
    public function store(CreateUserRequest $request, CreateUser $createUser)
    {
        return $this->respond($createUser->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a user.
     * <aside><ul><li>update-user</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     */
    public function update(UpdateUserRequest $request, int $id, UpdateUser $updateUser)
    {
        return $this->respond($updateUser->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a user.
     * <aside><ul><li>delete-user</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     */
    public function destroy(DeleteUserRequest $request, int $id, DeleteUser $deleteUser)
    {
        return $this->respondDeleted($deleteUser->execute($id));
    }

	/**
	* favouriteUsers()
	*
	* Lists users based on their favourite.
	* <aside><ul><li>list-user</li></ul></aside>
	* @urlParam favourites integer[] Favourite IDs Example: [1,2,3]
	*/
	public function favouriteUsers(string $favouriteIDs)
	{
		$favouriteIDs = explode(',', $favouriteIDs);
		abort(501, 'Favourite users not implemented');
		//return $this->respond(QueryBuilder::for(User::class)->whereHas('favourites', function ($q) use ($favouriteIDs) {$q->whereIn('id', $favouriteIDs);})->list());
	}

    /**
     * userPopoverContent()
     *
     * Reads and returns a user with the data needed for a popover.
     * <aside><ul><li>show-user</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     */
    public function userPopoverContent(ShowUserRequest $request, int $id)
    {
        $user = User::find($id);

        return $user;
    }

    public function companyUsers(ListUserRequest $request, int $companyID)
    {
        return QueryBuilder::for(User::class)->where('company_id', $companyID)->list();
    }

    public function chatInviteCompanyUsers(ListChatInviteUserRequest $request)
    {
        $companyID = request()->query('company');
        $chatID = request()->query('chat');

        // invitedChats is a relationship on the User model
        return QueryBuilder::for(User::class)
            ->where('company_id', $companyID)
            ->whereDoesntHave('invitedChats', function ($q) use ($chatID) {
                $q->where('chat_id', $chatID);
            })
            ->whereDoesntHave('chats', function ($q) use ($chatID) {
                $q->where('chat_id', $chatID);
            })
            ->list();
    }
}


//Generated 10-10-2023 10:05:12
