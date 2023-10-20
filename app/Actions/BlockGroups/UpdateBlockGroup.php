<?php

namespace App\Actions\BlockGroups;

use App\Models\BlockGroup;
use Spatie\QueueableAction\QueueableAction;

class UpdateBlockGroup
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $blockGroup = BlockGroup::findOrFail($id);

        $blockGroup->fill($data);

		if(isset($data['builder_category_id'])) {
			$blockGroup->builderCategory()->associate($data['builder_category_id']);
		}

        if (!$blockGroup->isDirty()) {
            $blockGroup->emitUpdated();
        }

        $blockGroup->save();

        return $blockGroup;
    }
}

//Generated 16-10-2023 10:39:10
