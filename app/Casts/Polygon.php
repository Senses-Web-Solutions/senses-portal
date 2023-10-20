<?php

namespace App\Casts;

use App\Casts\Postgis;
use InvalidArgumentException;
use Shapefile\Geometry\Polygon as PostgisPolygon;

class Polygon extends Postgis
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
        return $value ? (new PostgisPolygon())->initFromWKT($value) : null;
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
        if (!$value instanceof PostgisPolygon && !is_null($value)) {
            throw new InvalidArgumentException('The given value is not a Polygon instance.');
        }

        return $value ? $this->transformWKT($value) : null;
    }
}
