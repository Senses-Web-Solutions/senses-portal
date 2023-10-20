<?php

namespace App\Actions\Pages;

use App\Models\Page;
use Spatie\QueueableAction\QueueableAction;

class CreatePage
{
    use QueueableAction;

    public function execute(array $data)
    {

        $page = new Page($data);

		$page->status()->associate($data['status_id'] ?? null);
		
        $page->save();

        $page->buildContent($data['content']);

        return $page;
    }
}

//Generated 10-10-2023 14:43:35
