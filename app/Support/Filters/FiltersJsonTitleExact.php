<?php

namespace App\Support\Filters;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersJsonTitleExact implements Filter {
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereRaw('jsonb_array_length(jsonb_path_query_array('.$query->qualifyColumn($property).', \'$[*].title ? (@ == $title)\', \'{"title":"' . $value . '"}\')) > 0'); //todo escape vars
    }
}