<?php

namespace App\Actions\Pages;

use App\Models\Page;
use Spatie\QueueableAction\QueueableAction;

class DeletePage
{
    use QueueableAction;

    public function execute(int $id)
    {
        $page = Page::findOrFail($id);

        $page->delete();

        return $page;
    }
}

//Generated 10-10-2023 14:43:35
