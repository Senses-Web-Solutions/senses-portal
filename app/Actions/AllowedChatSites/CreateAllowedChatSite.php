<?php

namespace App\Actions\AllowedChatSites;

use App\Models\AllowedChatSite;
use Spatie\QueueableAction\QueueableAction;

class CreateAllowedChatSite
{
    use QueueableAction;

    public function execute(array $data)
    {
        $allowedChatSite = new AllowedChatSite($data);

        if (isset($data['company_id'])) {
            $companyID = $data['company_id'] ?? auth()->user()->company_id;
            $allowedChatSite->company()->associate($companyID);
        }

        $allowedChatSite->save();

        return $allowedChatSite;
    }
}

//Generated 27-10-2023 10:55:45
