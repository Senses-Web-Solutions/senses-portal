<?php

namespace App\Actions\Revenues;

use App\Models\Revenue;
use Spatie\QueueableAction\QueueableAction;

class CreateRevenue
{
    use QueueableAction;

    public function execute(array $data)
    {
        $revenue = new Revenue($data);

		if(isset($data['company_id'])) {
			$revenue->company()->associate($data['company_id']);
		}

        $revenue->save();

        return $revenue;
    }
}

//Generated 04-11-2023 16:09:26
