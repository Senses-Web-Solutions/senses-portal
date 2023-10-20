<?php

namespace App\Http\Controllers\Api;

use App\Models\UserSetting;
use App\Traits\ApiResponse;
use App\Http\Controllers\Api\Controller;
use App\Actions\UserSettings\DeleteUserSetting;
use App\Actions\UserSettings\DetermineUserSettingAction;
use App\Http\Requests\UserSettings\ListUserSettingRequest;
use App\Http\Requests\UserSettings\ShowUserSettingRequest;
use App\Http\Requests\UserSettings\DeleteUserSettingRequest;

/**
 * @group User Setting
 *
 * APIs for managing user-settings
 */
class UserSettingController extends Controller
{

use ApiResponse;

    /**
     * show()
     *
     * Reads and returns a user-setting.
     * <aside><ul><li>show-user-setting</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     * @urlParam user-setting integer User Setting String. Example: "task-table"
     */
    public function getUserSetting(ShowUserSettingRequest $request, int $userID, string $setting, DetermineUserSettingAction $determineUserSettingAction)
    {
        $action = $determineUserSettingAction->execute($setting);
        return $action->execute($userID, $setting);
    }

    /**
     * update()
     *
     * Updates, saves and returns a user.
     * <aside><ul><li>update-user-setting</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     * @urlParam user-setting integer User Setting String. Example: "task-table"
     */
    public function updateUserSetting(ShowUserSettingRequest $request, int $userID, string $setting, DetermineUserSettingAction $determineUserSettingAction)
    {
        $action = $determineUserSettingAction->execute($setting);
        return $this->respond($action->execute($userID, $setting, $request->all()));
    }


    /**
     * destroy()
     *
     * Deletes a user-setting.
     * <aside><ul><li>delete-user-setting</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     * @urlParam user-setting integer User Setting String. Example: "task-table"
     */
    public function destroy(DeleteUserSettingRequest $request, int $userID, string $setting, DeleteUserSetting $deleteUserSetting)
    {
        $userSetting = UserSetting::whereSetting($setting)->where('user_id', $userID)->first();
        if(!$userSetting) {
            return $this->respondDeleted(null);
        }
        return $this->respondDeleted($deleteUserSetting->execute($userSetting->id));
    }

}

//Generated 07-09-2021 09:54:24
