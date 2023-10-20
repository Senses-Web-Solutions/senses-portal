<?php
namespace App\Traits;

use Illuminate\Support\Str;

trait HasModelCache
{
    public function getCacheKeyAttribute()
    {
        return $this->cache_class_key . '-' . $this->id;
    }

    public function getCacheClassKeyAttribute()
    {
        return Str::kebab(class_basename($this));
    }

    public static function cacheKeyFromID($id)
    {
        $model = new self();
        return $model->cache_class_key . '-' . $id;
    }
}
