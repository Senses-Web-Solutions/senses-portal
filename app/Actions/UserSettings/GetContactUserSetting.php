<?php
namespace App\Actions\UserSettings;

use Illuminate\Support\Facades\Validator;

class GetContactUserSetting extends GetUserSetting
{

    public function validateSetting(array $data) {
        return $data;
    }

    public function getDefaultSetting() {
        return [ //TODO
            // 'export-download-ready' => ["email" => true, "sms" => true, "web" => true],
            // 'export-failed' => ["email" => true, "sms" => true, "web" => true],
            // 'message-received' => ["email" => true, "sms" => true, "web" => true],
            // 'redis-warning' => ["email" => true, "sms" => true, "web" => true],
            // 'registration-required' => ["email" => true, "sms" => true, "web" => true],
            // 'report-download-ready' => ["email" => true, "sms" => true, "web" => true],
            // 'report-failed' => ["email" => true, "sms" => true, "web" => true],
            // 'sign-in-required' => ["email" => true, "sms" => true, "web" => true],
            // 'test-notification' => ["email" => true, "sms" => true, "web" => true],
            // add notifications here
        ];

        // ! DONT FORGET TO RUN "php artisan notification:refresh_users" TO ADD NEW NOTIFICATION TO USER CONTACT SETTINGS
    }

}
