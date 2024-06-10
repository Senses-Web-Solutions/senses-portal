<?php

namespace App\Actions\AllowedChatSites;

use App\Models\AllowedChatSite;
use Spatie\QueueableAction\QueueableAction;

class UpdateAllowedChatSite
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $allowedChatSite = AllowedChatSite::findOrFail($id);

        $allowedChatSite->fill($data);

        if (isset($data['company_id'])) {
            $companyID = $data['company_id'] ?? auth()->user()->company_id;
            $allowedChatSite->company()->associate($companyID);
        }

        if (!$allowedChatSite->isDirty()) {
            $allowedChatSite->emitUpdated();
        }

        $allowedChatSite->save();

        return $allowedChatSite;
    }
}

//Generated 27-10-2023 10:55:45
