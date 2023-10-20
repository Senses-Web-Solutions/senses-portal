<?php

namespace App\Support\Filters;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersExactOrNull implements Filter {
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function($q) use ($property, $value) {
            $q->where($q->qualifyColumn($property), $value);
            $q->orWhereNull($q->qualifyColumn($property));
        });
    }
}