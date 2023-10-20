<?php

namespace App\Support\Filters;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersBetween implements Filter {
    public function __invoke(Builder $query, $value, string $property)
    {
        if(!is_array($value)) {
            return;
        }

        if(count($value) != 2) {
            return;
        }

        $startValue = $value[0] ?? null;
        $endValue = $value[1] ?? null;
        
        if($startValue == null || $endValue == null) {
            return;
        }

        $query->whereBetween($query->qualifyColumn($property), [$startValue, $endValue]);
    }
}