<?php

namespace App\Support\Filters;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersIntegerPartial implements Filter {
    public function __invoke(Builder $query, $value, string $property)
    {
        // if(!Str::contains($property, '.')) {
        //     $property = $query->getModel()->getTable() .'.'. $property;
        // }
        $query->whereRaw("LOWER(CAST(". $query->qualifyColumn($property). " as TEXT)) LIKE '%". $value . "%'");
    }
}