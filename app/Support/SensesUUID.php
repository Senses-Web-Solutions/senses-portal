<?php

namespace App\Support;

use Illuminate\Support\Str;

class SensesUUID {
    public static function generate() : string {
        return config('senses.client') .'-'.(string)Str::uuid();
    }
}
