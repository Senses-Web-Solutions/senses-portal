<?php
namespace App\Traits;

use App\Casts\Geometry;
use App\Scopes\PostgisScope;
use Illuminate\Support\Facades\DB;
use App\Scopes\PostgisTransformScope;
use Illuminate\Support\Facades\Schema;

trait HasPostgis
{
    public static function bootHasPostgis()
    {
        static::addGlobalScope(new PostgisTransformScope);
    }

    public function getInputSrid($inputSrid = null){
        if(is_null($inputSrid)) {
            $inputSrid = config('senses.geometry.input_srid', 4326);
        }
        return $inputSrid;
    }

    public function getOutputSrid($outputSrid = null){
        if(is_null($outputSrid)) {
            $outputSrid = config('senses.geometry.output_srid', 4326);
        }
        return $outputSrid;
    }

    public function scopeWhereInBoundingBox($query, $column, $minLat, $minLng, $maxLat, $maxLng, $inputSrid = null)
    {
        $inputSrid = $this->getInputSrid($inputSrid);
        $configInputSrid = $this->getInputSrid();

        return $query->whereRaw("ST_Intersects(ST_TRANSFORM(ST_MakeEnvelope(?, ?, ?, ?, $inputSrid),$configInputSrid), $column)", [$minLat, $minLng, $maxLat, $maxLng]);
    }

    public function scopeWhereInPolygon($query, $column, $lineString, $inputSrid = null)
    {
        $inputSrid = $this->getInputSrid($inputSrid);
        $outputSrid = $this->getOutputSrid();

        return $query->whereRaw("ST_Intersects(ST_Transform(ST_PolygonFromText(?, $inputSrid), $outputSrid), ?)", [$lineString, $column]);
    }

    public function scopeWhereInRadius($query, $lat, $lng, $radius = 2000)
    {
        $inputSrid = $this->getInputSrid();
        $outputSrid = $this->getOutputSrid();

        return $query->whereRaw("ST_DWithin(geom, ST_Transform(ST_GeomFromText('Point($lng $lat)', $outputSrid), $inputSrid), $radius)");
    }

    public function scopeBaseWKT($query) {
        return (new PostgisScope())->apply($query, $query->getModel());
    }

    public function newQueryWithoutScopes()
    {
        return parent::newQueryWithoutScopes()->withGlobalScope(PostgisTransformScope::class, $this->getGlobalScope(PostgisTransformScope::class)); //remember when I said no scopes? I lied.
    }

    public function postgisColumnsCacheKey() {
        return 'postgis-'. $this->getTable() .'-table-columns';
    }

    public function clearPostgisColumnsCache() {
        cache()->forget($this->postgisColumnsCacheKey());
    }

    public function getPostgisColumns() {
        return cache()->rememberForever($this->postgisColumnsCacheKey(), fn() => Schema::getColumnListing($this->getTable()));
    }

    public function getBaseGeometry($field = 'geom') {
        $wkt = $this->$field?->getWKT();
        if(!$wkt) {
            return null;
        }

        $inputSrid = $this->getInputSrid();
        $outputSrid = $this->getOutputSrid();

        $baseWKT = DB::query()->selectRaw("st_astext(st_transform(ST_GeomFromText(?,$outputSrid), $inputSrid)) as base_wkt", [$wkt])->value('base_wkt');
        $geometryCast = new Geometry();

        return $geometryCast->getGeometryClass($baseWKT)->get($this, null, $baseWKT, []);
    }

    public function getConvertedGeometry($field = 'geom') {
        //assumes ->geom is current a baseWKT, so we'll convert and then reload geom to the converted version.
        $baseWKT = $this->$field->getWKT();
        if(!$baseWKT) {
            return null;
        }

        $inputSrid = $this->getInputSrid();
        $outputSrid = $this->getOutputSrid();

        $convertedWKT = DB::query()->selectRaw("st_astext(st_transform(st_geomfromtext(?, $inputSrid), $outputSrid)) as converted_wkt", [$baseWKT])->value('converted_wkt');

        $geometryCast = new Geometry();
        return $geometryCast->getGeometryClass($convertedWKT)->get($this, null, $convertedWKT, []);
    }

    // public function getGeoJson(array $infrastructureAssetTypes)
    // {
    //     //hook into predetermined postgis functions, but provide an eloquent end point.

    //     return $this->raw("

    //     SELECT t.infrastructure_asset_type_id,

            // jsonb_build_object(
            // 'type', 'FeatureCollection',
            // 'features', COALESCE(jsonb_agg(jsonb_build_object(
            //         'type', 'Feature',
            //         'geometry', to_jsonb(ST_Transform(a.geom, 4258)),
            //         'properties'  , '{}'
            //         ) ORDER BY a.id
            //     ) FILTER (WHERE a.id IS NOT NULL), '[]')
            // ) AS geojson

    //    FROM
    //    (SELECT v::bigint AS infrastructure_asset_type_id FROM jsonb_array_elements('?') AS j(v)) AS t
    //    LEFT JOIN   (?)   AS a
    //    ON a.infrastructure_asset_type_id = t.infrastructure_asset_type_id
    //     GROUP BY t.infrastructure_asset_type_id
    //     ORDER BY t.infrastructure_asset_type_id;
    //     ", [json_encode($infrastructureAssetTypes), $this->toSql()])->getValue();
    // }
}
