<?php

namespace App\Support\Filters;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersGreaterThan implements Filter {
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where($query->qualifyColumn($property), '>', $value);
    }
}