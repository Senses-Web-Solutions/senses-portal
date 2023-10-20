<?php

namespace App\Support\Filters;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersDateTimeExact implements Filter {
    public function __invoke(Builder $query, $value, string $property)
    {
        $value = Str::beforeLast($value, ':');
        $startDate = $value .':00';
        $endDate = $value . ':59';
        $query->whereBetween($query->qualifyColumn($property), [$startDate, $endDate]);
    }
}