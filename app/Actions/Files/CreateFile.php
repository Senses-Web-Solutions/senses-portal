<?php

namespace App\Actions\Files;

use App\Models\File;
use App\Models\Task;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Actions\Files\RelateFormFiles;
use Illuminate\Support\Facades\Storage;
use App\Actions\Tasks\FindRelatedTaskID;
use Spatie\QueueableAction\QueueableAction;
use App\Actions\RestoreDeletedRelationships;
use App\Actions\Files\DetermineFileDirectory;
use App\Actions\UserActionLogs\CreateUserActionLog;
use Illuminate\Database\Eloquent\Relations\Relation;

class CreateFile
{
    use QueueableAction;

    public function execute(array $data)
    {
		$public = $data['public'] ?? true;

		$targetDisk = $this->determineDisk($data, $public);
		$currentDisk = $targetDisk;
        $directory = $this->determineDirectory();

        if (isset($data['file'])) {
            $requestFile = $data['file'];

            $name = pathinfo($requestFile->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower($requestFile->getClientOriginalExtension());
            $storedName = $name . '_' . now()->timestamp . '.' . $extension;


            $mimeType = $requestFile->getClientMimeType();
            $size = $requestFile->getSize();

            $path = Storage::disk($currentDisk)->putFileAs($directory, $requestFile, $storedName, ['visibility' => $public ? 'public' : 'private']);
        }
        else if(isset($data['stored_file'])) {
            $name = $data['stored_file']['name'];
            $extension = $data['stored_file']['extension'];
            $storedName = $data['stored_file']['stored_name'];
            $mimeType = $data['stored_file']['mime_type'];
            $size = $data['stored_file']['size'];
            $currentDisk = $data['stored_file']['current_disk'];
            $path = $data['stored_file']['path'];
        }


		$file = new File();

        $file->fill([
			'name' => $name,
			'extension' => $extension,
			'stored_name' => $storedName,
			'path' => $path,
			'mime_type' => $mimeType,
			'size' => $size,
			'disk' => $currentDisk,
			'public' => $public,
			'folder' => null,
		]);

        $file->save();

		$this->attachFileables($file, $data);

		app(GenerateFilePreview::class)->execute($file);

		return $file->append('url');
    }

	public function determineDisk(array $data, bool $public) : String {
        if($public) {
            return 'public';
        }

		$disk = $data['disk'] ?? 'local';

		return $disk;
	}

	public function attachFileables(File &$file, array $data) : File {
		if(!isset($data['fileables'])) {
			return $file;
		}

		foreach($data['fileables'] as $fileable) {
			$fileableId = null;
			if (array_key_exists('model', $fileable) && $fileable['model']) {
				$fileableId = $fileable['model']?->id;
			} else if (array_key_exists('fileable_id', $fileable)) {
				$fileableId = $fileable['fileable_id'];
			}
			$relation = Str::camel(Str::plural($fileable['fileable_type']));
			$file->$relation()->syncWithoutDetaching($fileableId); //right now assumes fileable function exists on tasks
			$file->emitAttachingFileables($fileable['fileable_type'], $fileableId);
		}

		return $file;
	}

    public function determineDirectory() {

        return 'uploads/' . now()->format('Y') . '/' . now()->format('m');
    }
}