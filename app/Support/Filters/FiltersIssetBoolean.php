<?php

namespace App\Support\Filters;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersIssetBoolean implements Filter {
    public function __invoke(Builder $query, $value, string $property)
    {
        if($value) {
            $query->whereNotNull($query->qualifyColumn($property));
        }
        else {
            $query->whereNull($query->qualifyColumn($property));
        }
    }
}