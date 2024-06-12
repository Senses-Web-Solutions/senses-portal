<?php

namespace App\Actions\Files;

use App\Models\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class CreateOrientatedImageFromFile
{
    public function execute(File $file)
    {
        if ($file->pending) {
            return null;
        }

        $stream = Storage::disk($file->disk)->readStream($file->path);
        $image = Image::make($stream);

        if (!is_resource($stream)) {
            return $image;
        }

        if (strtolower($file->mime_type) === 'application/pdf') {
            return $image;
        }

        // $data = exif_read_data($stream);
        $data = rescue(function () use ($stream) {
            return exif_read_data($stream);
        }, null, report: false);

        if (empty($data) || !isset($data['Orientation'])) {
            return $image;
        }

        switch ($data['Orientation']) {
            case 2:
                $image->flip();
                break;

            case 3:
                $image->rotate(180);
                break;

            case 4:
                $image->rotate(180)->flip();
                break;

            case 5:
                $image->rotate(270)->flip();
                break;

            case 6:
                $image->rotate(270);
                break;

            case 7:
                $image->rotate(90)->flip();
                break;

            case 8:
                $image->rotate(90);
                break;
        }

        return $image;
    }
}
