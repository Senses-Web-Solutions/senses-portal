<?php
namespace App\Actions\UserSettings;

use App\Models\User;
use Illuminate\Validation\Validator;

class GetUserImpersonationSetting extends GetUserSetting
{
    public $createAutomatically = false;

    public function validateSetting(array $data) {
        $user = getActualCurrentUser();
        if(!$user->can('impersonate-user')) {
            abort(403, 'Impersonation is not allowed for this user.');
        }

        if($user->isNotAn('senses') && isset($data['user_id'])) {
            $impersonationUser = User::find($data['user_id']);
            if($impersonationUser->isAn('senses')) {
                abort(403, 'Impersonation is not allowed for this user.');
            }
        }

        return $data;
    }

    public function getDefaultSetting() {
        return [
            'user_id' => null,
        ];
    }
}
