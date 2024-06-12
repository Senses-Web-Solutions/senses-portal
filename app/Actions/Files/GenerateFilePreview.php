<?php

namespace App\Actions\Files;

use App\Models\File;
use Spatie\QueueableAction\QueueableAction;
use App\Actions\Files\GenerateFileThumbnail;

class GenerateFilePreview
{
    use QueueableAction;

    /**
     * Create a new action instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Prepare the action for execution, leveraging constructor injection.
    }

    /**
     * Execute the action.
     *
     * @return mixed
     */
    public function execute(File $file, Int $width = 120, Int $height = 120, Bool $overwrite = false): File|Null
    {
        return app(GenerateFileThumbnail::class)->execute(
            $file,
            'preview',
            $width,
            $height,
            $overwrite,
        );
    }
}
