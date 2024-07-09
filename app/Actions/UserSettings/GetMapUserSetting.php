<?php

namespace App\Actions\UserSettings;

class GetMapUserSetting extends GetUserSetting
{
    public $createAutomatically = false;

    public function getDefaultSetting()
    {
        return [
            'animate' => true,
            'ants' => true,
            'three_dimensional' => true,
            'light' => true,
        ];
    }
}
