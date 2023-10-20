<?php

namespace App\Actions\Pages;

use App\Models\Page;
use Spatie\QueueableAction\QueueableAction;

class UpdatePage
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $page = Page::findOrFail($id);

        $page->fill($data);

		if(isset($data['status_id'])) {
			$page->status()->associate($data['status_id']);
		}

        if (!$page->isDirty()) {
            $page->emitUpdated();
        }

        $page->save();

        $page->buildContent($data['content']);


        return $page;
    }
}

//Generated 10-10-2023 14:43:35
