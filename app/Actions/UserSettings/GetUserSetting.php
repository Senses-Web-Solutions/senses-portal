<?php
namespace App\Actions\UserSettings;

use App\Models\UserSetting;
use App\Traits\ApiResponse;
use Senses\TaggedCache\Facades\TaggedCache;

class GetUserSetting
{

    protected $userID = null;

    public function execute(int $userID, string $setting, array $data = null)
    {
        $this->userID = $userID;
        $createAutomatically = property_exists($this, 'createAutomatically') ? $this->createAutomatically : false;
        $cached = property_exists($this, 'cached') ? $this->cached : true;
        if($cached) {
            return $this->getCachedSetting($userID, $setting, $data, $createAutomatically);
        }

        return $this->getSetting($userID, $setting, $data, $createAutomatically);
    }

    public function getSetting(int $userID, string $setting, array $data = null, bool $createAutomatically = true) {
        if($data) {
            $data = $this->validateSetting($data);
        }

        $userSetting = UserSetting::whereSetting($setting)->where('user_id', $userID)->first();

        if($userSetting && $data) {
            $userSetting = $this->updateUserSetting($userSetting, $data);
        }
        else if (!$userSetting) {

            $save = true;
            if(!$data) {
                $data = $this->getDefaultSetting();
                $save = $createAutomatically;
            }

            $userSetting = $this->createUserSetting($userID, $setting, $data, save: $save);
        }

        return $userSetting;
    }

    public function getCachedSetting(int $userID, string $setting, array $data = null, bool $createAutomatically = true) {
        if($data) {
            TaggedCache::flush('user-settings-'.$userID . '-' . $setting);
        }

        //todo swap to response cache for header, will require internal use of ->getData() on it
        return TaggedCache::rememberForever('user-settings-'.$userID . '-' . $setting, function() use ($userID, $setting, $data, $createAutomatically) {
            return $this->getSetting($userID, $setting, $data, $createAutomatically);
        });
    }

    public function validateSetting(array $data) {
        return $data;
    }

    public function getDefaultSetting() {
        return [];
    }

    public function updateUserSetting(UserSetting $userSetting, array $data) {
        $userSetting->data = $data;
        $userSetting->save();
        return $userSetting;
    }

    public function createUserSetting(int $userID, string $setting, array $data, bool $save = true) {

        $userSetting = new UserSetting([
            'setting' => $setting,
            'data' => $data
        ]);
        $userSetting->user()->associate($userID);
        if($save) {
            $userSetting->save();
        }
        return $userSetting;
    }

}
