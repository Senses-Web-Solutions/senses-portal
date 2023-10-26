<?php

namespace App\Support\Services;

use Carbon\Carbon;
use App\Models\Weather;
use Shapefile\Geometry\Geometry;
use App\Interfaces\Services\WeatherService;

class NullWeather implements WeatherService
{

    public function getFromLatLng(Weather $weather, float $lat, float $lng, Carbon $date = null) : Weather
    {
        return $weather;
    }

    public function getFromGeom(Weather $weather, Geometry $geom, Carbon $date = null) : Weather
    {
        $lat = $geom->getY();
        $lng = $geom->getX();
        
        return $this->getFromLatLng($weather, $lat, $lng, date: $date);
    }

}
