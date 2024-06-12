<?php

namespace App\Actions\Files;

use App\Models\File;
use Maestroerror\HeicToJpg;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Spatie\QueueableAction\QueueableAction;

class ConvertHEICToJPG
{
    use QueueableAction;

    public function execute(int|File $file, $save = true)
    {
        return rescue(function () use (&$file, $save) {
            if (is_int($file)) {
                $file = File::findOrFail($file);
            }
            $jpgPath = str_replace(".heic", ".jpg", $file->path);


            $jpgBinary = HeicToJpg::convertFromUrl(File::generateUrl($file->disk, $file->path, false, $file->id))->get();

            $image = Image::make($jpgBinary);

            $uploaded = Storage::disk($file->disk)->writeStream($jpgPath, $image->stream('jpg')->detach(), ['visibility' => 'public']);


            if ($uploaded) {
                $file->fill([
                    'extension' => "jpg",
                    'mime_type' => 'image/jpeg',
                    'path' => $jpgPath,
                    'stored_name' => str_replace(".heic", ".jpg", $file->stored_name),
                    'size' => Storage::disk($file->disk)->size($jpgPath),
                ]);
                if ($save) {
                    $file->save();
                }
            }

            return $file;
        }, function ($e) use (&$file) {
            // throw $e; //debugging
            return $file;
        });
    }
}
