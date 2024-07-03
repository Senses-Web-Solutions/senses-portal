<?php

namespace App\Support;

use Illuminate\Support\Str;

class SensesUUID {
    public static function generate() : string {
        return env('SENSES_CLIENT') . '-' . (string)Str::uuid();
    }
}
