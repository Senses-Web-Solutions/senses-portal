<?php

namespace App\Casts;

use App\Casts\Postgis;
use InvalidArgumentException;
use Shapefile\Geometry\GeometryCollection as PostgisGeometryCollection;

class GeometryCollection extends Postgis
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
        return $value ? (new PostgisGeometryCollection())->initFromWKT($value) : null;
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
        if (!$value instanceof PostgisGeometryCollection && !is_null($value)) {
            throw new InvalidArgumentException('The given value is not a GeometryCollection instance.');
        }

        return $value ? $this->transformWKT($value) : null;
    }
}
