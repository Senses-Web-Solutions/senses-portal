<?php

namespace App\Casts;

use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Shapefile\Geometry\Point as PostgisPoint;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Query\Expression;

abstract class Postgis implements CastsAttributes, SerializesCastableAttributes
{
    public function transformWKT($value)
    {
        $inputSrid = config('senses.geometry.input_srid', 27700);
        $outputSrid = config('senses.geometry.output_srid', 4326);
        return $value ? DB::raw("ST_Transform(ST_GeomFromText('{$value->getWKT()}',$outputSrid), $inputSrid)") : null;
    }

    /**
     * Get the serialized representation of the value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function serialize($model, string $key, $value, array $attributes)
    {
        if($value instanceof Expression) {
            return $value->getValue();
        }
        return $value ? $value->getArray() : null;
    }
}
