<?php
namespace App\Actions\UserSettings;

use App\Models\UserSetting;
use Senses\TaggedCache\Facades\TaggedCache;
use Spatie\QueueableAction\QueueableAction;

class DeleteUserSetting
{
    use QueueableAction;

    public function execute(int $id)
    {
        $userSetting = UserSetting::findOrFail($id);
        $userSetting->delete();
        TaggedCache::forget($userSetting->cacheKey);
        return $userSetting;
    }
}

//Generated 07-09-2021 09:54:24
