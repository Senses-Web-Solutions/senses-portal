<?php

namespace App\Actions\Revenues;

use App\Models\Revenue;
use Spatie\QueueableAction\QueueableAction;

class UpdateRevenue
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $revenue = Revenue::findOrFail($id);

        $revenue->fill($data);

		if(isset($data['company_id'])) {
			$revenue->company()->associate($data['company_id']);
		}

        if (!$revenue->isDirty()) {
            $revenue->emitUpdated();
        }

        $revenue->save();

        return $revenue;
    }
}

//Generated 04-11-2023 16:09:26
