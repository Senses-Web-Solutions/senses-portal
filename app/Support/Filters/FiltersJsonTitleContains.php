<?php

namespace App\Support\Filters;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersJsonTitleContains implements Filter {
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereRaw($query->qualifyColumn($property) . "::text ilike ?", ['%'.$value .'%']); //todo find less lame way of json where
    }
}