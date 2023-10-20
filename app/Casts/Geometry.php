<?php

namespace App\Casts;

use App\Casts\Point;
use App\Casts\Polygon;
use App\Casts\Postgis;
use App\Casts\LineString;
use App\Casts\MultiPoint;
use App\Casts\MultiPolygon;
use Illuminate\Support\Str;
use InvalidArgumentException;
use App\Casts\MultiLineString;
use App\Casts\GeometryCollection;
use Illuminate\Database\Query\Expression;
use Shapefile\Geometry\Point as PostgisPoint;
use Shapefile\Geometry\Polygon as PostgisPolygon;
use Shapefile\Geometry\Geometry as PostgisGeometry;
use Shapefile\Geometry\Geometry as PostgisMultiPolygon;
use Shapefile\Geometry\Linestring as PostgisLineString;
use Shapefile\Geometry\MultiPoint as PostgisMultiPoint;
use Shapefile\Geometry\MultiLinestring as PostgisMultiLineString;
use Shapefile\Geometry\GeometryCollection as PostgisGeometryCollection;

class Geometry extends Postgis
{
    protected $wktTypes = [
        'POINT' => Point::class,
        'POINTZ' => Point::class,
        'POINT Z' => Point::class,

        'LINESTRING' => LineString::class,
        'LINESTRINGZ' => LineString::class,
        'LINESTRING Z' => LineString::class,

        'POLYGON' => Polygon::class,
        'POLYGONZ' => Polygon::class,
        'POLYGON Z' => Polygon::class,

        'MULTIPOINT' => MultiPoint::class,
        'MULTIPOINTZ' => MultiPoint::class,
        'MULTIPOINT Z' => MultiPoint::class,

        'MULTILINESTRING' => MultiLineString::class,
        'MULTILINESTRINGZ' => MultiLineString::class,
        'MULTILINESTRING Z' => MultiLineString::class,

        'MULTIPOLYGON' => MultiPolygon::class,
        'MULTIPOLYGONZ' => MultiPolygon::class,
        'MULTIPOLYGON Z' => MultiPolygon::class,

        'GEOMETRYCOLLECTION' => GeometryCollection::class,
    ];

    protected $postgisCasts = [
        PostgisGeometryCollection::class => GeometryCollection::class,
        PostgisLineString::class => LineString::class,
        PostgisMultiLineString::class => MultiLineString::class,
        PostgisMultiPoint::class => MultiPoint::class,
        PostgisMultiPolygon::class => MultiPolygon::class,
        PostgisPoint::class => Point::class,
        PostgisPolygon::class => Polygon::class,
    ];

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
        return optional($this->getGeometryClass($value))->get($model, $key, $value, $attributes);
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
        if ($value instanceof Expression) {
            return $value;
        }

        if (!$value instanceof PostgisGeometry && !is_null($value)) {
            throw new InvalidArgumentException('The given value is not an Geometry instance.');
        }

        // dd($this->postgisCasts[get_class($value)]);

        return $value ? (new $this->postgisCasts[get_class($value)])->set($model, $key, $value, $attributes) : null;
    }

    public function getGeometryClass($value)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof Expression) {
            return $value;
        }

        $type = Str::before($value, '(');

        if (Str::after($type, ' ')  == 'EMPTY') {
            return null;
        }

        // dd($value, $type);
        if (!isset($this->wktTypes[$type])) {
            throw new InvalidArgumentException("Unknown geometry class for type {$type}.");
        }

        return new $this->wktTypes[$type]();
    }
}
