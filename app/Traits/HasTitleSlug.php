<?php
namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

trait HasTitleSlug
{
    public function setTitleAttribute($value)
    {
        $slug = Str::slug($value);

        if(static::query()->where('slug', $slug)->exists()) {
            $validator = validator(
                ['slug' => $slug], 
                ['slug' => ['string', Rule::unique($this->getTable(), 'slug')->ignore($this->id)]], 
            []);
            $validator->validate();
        }


        $this->attributes['slug'] = $slug;
        $this->attributes['title'] = $value;
    }
}
