<?php

namespace App\Actions\BlockGroups;

use App\Models\BlockGroup;
use Spatie\QueueableAction\QueueableAction;

class CreateBlockGroup
{
    use QueueableAction;

    public function execute(array $data)
    {
        $blockGroup = new BlockGroup($data);

		if(isset($data['builder_category_id'])) {
			$blockGroup->builderCategory()->associate($data['builder_category_id']);
		}

        $blockGroup->save();

        return $blockGroup;
    }
}

//Generated 16-10-2023 10:39:10
