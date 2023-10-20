<?php

namespace App\Actions\UserSettings;

use Illuminate\Support\Facades\Validator;

class GetTableUserSetting extends GetUserSetting
{
    public function validateSetting(array $data)
    {
        $templates = [];

        if (isset($data['templates'])) {
            foreach ($data['templates'] as $key => $template) {
                //for any legacy ones in testing that have been made without titles - just delete
                if ($key == 'null' || !isset($key)) {
                    unset($data['templates'][$key]);
                }
            }
        }

        return $data;
    }

    public function getDefaultSetting()
    {
        return [
            'currentTemplateName' => 'Default',
            'templates' => [
                'Default' => [
                    'sort' => null,
                    'order' => 'desc',
                    'filters' => [],
                    'hiddenFields' => [],
                    'fieldOrder' => null,
                    'limit' => 25
                ]
            ]
        ];
    }
}
