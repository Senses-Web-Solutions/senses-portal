<?php

namespace App\Actions\Files;

use App\Models\File;
use Spatie\QueueableAction\QueueableAction;

class UpdateFile
{
    use QueueableAction;

    public function execute(int $id, array $data)
    {
        $file = File::findOrFail($id);

        $file->fill($data);

        if (!$file->isDirty()) {
            $file->emitUpdated();
        }

        $file->save();

        return $file;
    }
}

//Generated 09-10-2023 13:46:51
