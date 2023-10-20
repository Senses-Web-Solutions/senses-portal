<?php
namespace App\Traits;

use App\Models\File;

trait HasFiles
{
    public function files()
    {
        return $this->morphToMany(File::class, 'fileable')->withTimestamps();
    }
}
