<?php

namespace App\Traits;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAddress
{

    public function getAddressAttribute(): string
    {
        $addressArray = [];

        if (isset($this->name) && isset($this->street)) {
            array_push($addressArray, $this->name . ' ' . $this->street);
        } else if (isset($this->name)) {
            array_push($addressArray, $this->name);
        } else if (isset($this->street)) {
            array_push($addressArray, $this->street);
        }

        $fields = ['town', 'city', 'county', 'country', 'postcode'];

        foreach ($fields as $field) {
            if (isset($this[$field])) {
                array_push($addressArray, $this[$field]);
            }
        }

        return implode(', ', array_filter($addressArray));
    }

}
