<?php
namespace App\Traits;

trait HasTextColour
{
    public function setColourAttribute($value)
    {
        $this->attributes['text_colour'] = textColour($value);
        $this->attributes['colour'] = $value;
    }
}
