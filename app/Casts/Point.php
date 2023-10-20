<?php

namespace App\Casts;

use App\Casts\Postgis;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Shapefile\Geometry\Point as PostgisPoint;

class Point extends Postgis
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        if($value instanceof \Illuminate\Database\Query\Expression) {
            $value = Str::between($value->getValue(), "ST_GeomFromText('" ,"'"); //some point we end up with our initial raw query, not sure how.
        }

        if($value == 'POINT EMPTY') {
            dd($value);
        }
        
 		return $value ? (new PostgisPoint())->initFromWKT($value) : null;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {

        if (!$value instanceof PostgisPoint && !is_null($value)) {
            throw new InvalidArgumentException('The given value is not a Point instance.');
        }

        return $value ? $this->transformWKT($value) : null;
    }
}
