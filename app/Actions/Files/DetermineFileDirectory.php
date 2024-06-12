<?php

namespace App\Actions\Files;

use App\Models\File;
use Spatie\QueueableAction\QueueableAction;

class DetermineFileDirectory
{
    use QueueableAction;

    protected $defaultDirectory;

    /**
     * Create a new action instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->defaultDirectory = 'uploads/' . now()->format('Y') . '/' . now()->format('m');
    }

    /**
     * Execute the action.
     *
     * @return mixed
     */
    public function execute(array $data, File $file = null): array
    {
        $directory = $this->determineDirectory($data, $file);
        $folder = $this->determineFolder($directory, $data);

        if ($folder) {
            $directory .= '/' . $folder;
        }

        return [$directory, $folder];
    }

    public function determineDirectory(array $data, $file = null)
    {
        $directory = $this->defaultDirectory;

        if (!isset($data['fileables'])) {
            return $directory;
        }

        return $directory;
    }

    public function determineFolder(String $directory, array $data)
    {

        if ($directory == $this->defaultDirectory) {
            return null; //don't sub directory the default uploads folder
        }

        if (isset($data['folder'])) {
            return $data['folder'];
        }

        if (isset($data['app_id'])) {
            return 'on-site'; //todo is it possible to link to a step folder? (needs to find the requirement, thus the step it fulfills)
        }

        return null;
    }
}
