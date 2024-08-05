<?php

namespace App\Scopes;

use App\Scopes\PostgisScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Scope;

class PostgisTransformScope extends PostgisScope implements Scope
{
    public function postgisSQL(string $column, string $alias)
    {
        $outputSrid = config('senses.geometry.output_srid', 4326);
        return DB::raw("ST_AsText(ST_Transform($column, $outputSrid)) as $alias");
    }
}
