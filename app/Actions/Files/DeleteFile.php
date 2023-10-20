<?php

namespace App\Actions\Files;

use App\Models\File;
use Spatie\QueueableAction\QueueableAction;

class DeleteFile
{
    use QueueableAction;

    public function execute(int $id)
    {
        $file = File::findOrFail($id);

        $file->delete();

        return $file;
    }
}

//Generated 09-10-2023 13:46:51
