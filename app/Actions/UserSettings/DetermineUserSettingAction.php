<?php
namespace App\Actions\UserSettings;

use Illuminate\Support\Str;
use Spatie\QueueableAction\QueueableAction;

class DetermineUserSettingAction
{
    use QueueableAction;

    protected $actions = [
        'table' => GetTableUserSetting::class,
        'impersonation' => GetUserImpersonationSetting::class,
        'contact' => GetContactUserSetting::class,
    ];

    public function execute(string $setting)
    {
        if(Str::startsWith($setting, 'table-')) {
            $setting = 'table';
        }
        else if(Str::startsWith($setting, 'contact-')) {
            $setting = 'contact';
        }

        if(!isset($this->actions[$setting])) {
            abort(400, 'No setting found for ' . $setting);
        }

        return app($this->actions[$setting]);
    }
}
