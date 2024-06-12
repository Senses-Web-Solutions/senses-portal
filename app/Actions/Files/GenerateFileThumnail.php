<?php

namespace App\Actions\Files;

use Exception;
use Carbon\Carbon;
use App\Models\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Spatie\QueueableAction\QueueableAction;
use App\Actions\Files\CreateOrientatedImageFromFile;

class GenerateFileThumbnail
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
    public function execute(File $file, string $type, int $width = 120, int $height = 120, bool $overwrite = false, bool $watermark = false, bool $aspectRatio = false): File|Null
    {
        return rescue(function () use ($file, $type, $width, $height, $overwrite, $watermark, $aspectRatio) {

            if (Str::startsWith(strtolower($file->mime_type), 'image/')) {
                return $this->generateImagePreview($file, $type, $width, $height, $overwrite, $watermark, $aspectRatio);
            } else if (strtolower($file->mime_type) === 'application/pdf') {
                return $this->generateImagePreview($file, $type, $width, $height, $overwrite, $watermark, $aspectRatio);
            }

            return null;
        }, null, report: true);
    }

    public function generateImagePreview(File $file, string $type, int $width, int $height, bool $overwrite = false, bool $watermark = false, bool $aspectRatio = false): File|Null
    {
        if (!Storage::disk('local')->exists('public/previews')) {
            Storage::disk('local')->makeDirectory('public/previews');
        }

        if (!extension_loaded('imagick')) {
            throw new Exception('Imagick not installed.'); //requires imagick
        }

        $diskField = $type . '_disk';
        $pathField = $type . '_path';

        $disk = 'local';
        $extension = 'webp';
        $path = 'public/previews/file-' . $file->id . '-' . $type . '.' . $extension;

        //use exists to check if disk is available, then fallback to local
        $exists = rescue(
            function () use (&$disk, &$path) {
                return Storage::disk($disk)->exists($path);
            },
            function () use (&$disk, &$path) {
                $disk = 'local';
                return Storage::disk($disk)->exists($path);
            },
            report: false
        );
        if ($exists) {
            if ($overwrite) {
                Storage::disk($disk)->delete($path);
            } else {
                $file->$diskField = $disk;
                $file->$pathField = $path;
                $file->saveQuietlyWithRevisions();
                $file->emitPreviewGenerated();
                return $file;
            }
        }

        $image = app(CreateOrientatedImageFromFile::class)->execute($file);

        if (!$image) {
            return $file;
        }

        if ($aspectRatio) {
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } else {
            $image->resize($width, $height);
        }

        if ($watermark) {
            $offset = 20;
            if (str_contains($file->uuid, '_app_') && str_contains($file->name, 'photo') && config('client.files.add_datetime_watermark', false)) {
                try {
                    $image->text(Carbon::createFromTimestampMs(Str::afterLast($file->name, "-"))->format('jS F Y H:i:s'), $image->width() - 20, $image->height() - $offset, function ($font) {
                        $font->file(base_path('public/fonts/Roboto-Black.ttf'));
                        $font->size(12);
                        $font->color('#ffffff');
                        $font->align('right');
                        $font->valign('bottom');
                    });

                    $offset += 20;
                } catch (Exception $e) {
                    logger($e);
                    logger($file->name);
                }

                if ($file->task?->company?->title) {
                    $image->text($file->task->company->title, $image->width() - 20, $image->height() - $offset, function ($font) {
                        $font->file(base_path('public/fonts/Roboto-Black.ttf'));
                        $font->size(12);
                        $font->color('#ffffff');
                        $font->align('right');
                        $font->valign('bottom');
                    });
                }
            }
        }

        $uploaded = Storage::disk($disk)->writeStream($path, $image->stream($extension)->detach(), ['visibility' => 'public']);
        if (!$uploaded) {
            $disk = null;
            $path = null;
        }

        $file->$diskField = $disk;
        $file->$pathField = $path;
        $file->saveQuietlyWithRevisions();
        $file->emitPreviewGenerated();
        return $file;
    }
}
