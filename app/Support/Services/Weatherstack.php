<?php

namespace App\Support\Services;

use App\Enums\WeatherType;
use Carbon\Carbon;
use App\Models\Weather;
use Illuminate\Support\Str;
use Shapefile\Geometry\Geometry;
use App\Interfaces\Services\WeatherService;
use Illuminate\Support\Facades\Http;


class Weatherstack implements WeatherService
{

    public function getFromLatLng(Weather $weather, float $lat, float $lng, Carbon $date = null) : Weather
    {

        $response = Http::timeout(30)->get("http://api.weatherstack.com/current?access_key=" . env('WEATHERSTACK_API_KEY') . "&query=" . $lng . "," . $lat);
        $data = $response->json();
        if ($response->successful() && !isset($data['code'])) {
        return $this->transformWStack($weather, $data);
        }
        
        return $weather;
        //return $this->transformWStack($weather, $data);
    }

    public function getFromGeom(Weather $weather, Geometry $geom, Carbon $date = null) : Weather
    {
        $lat = $geom->getY();
        $lng = $geom->getX();
        
        return $this->getFromLatLng($weather, $lat, $lng, date: $date);
    }

    static function transformWStack(Weather $weather, array $data) : Weather {
        $type = null;
        if (isset($data['current']['weather_descriptions'])) {
            $type = strtolower($data['current']['weather_descriptions'][0]);
        }
        
        $type = match($type) {
            'sunny' => WeatherType::SUNNY,
            'partly cloudy' => WeatherType::PARTLY_CLOUDY,
            'cloudy' => WeatherType::CLOUDY,
            'overcast' => WeatherType::OVERCAST,
            'mist' => WeatherType::MIST,
            'patchy rain possible' => WeatherType::LIGHT_RAIN,
            'patchy snow possible' => WeatherType::LIGHT_SNOW,
            'patchy sleet possible' => WeatherType::LIGHT_SLEET,
            'patchy freezing drizzle possible' => WeatherType::LIGHT_SLEET,
            'thundery outbreaks possible' => WeatherType::STORMS,
            'blowing snow' => WeatherType::LIGHT_SNOW,
            'blizzard' => WeatherType::HEAVY_SNOW,
            'fog' => WeatherType::FOG,
            'freezing fog' => WeatherType::FOG,
            'patchy light rain' => WeatherType::LIGHT_RAIN,
            'light rain' => WeatherType::LIGHT_RAIN,
            'moderate rain at times' => WeatherType::RAIN,
            'moderate rain' => WeatherType::RAIN,
            'heavy rain at times' => WeatherType::RAIN,
            'heavy rain' => WeatherType::HEAVY_RAIN,
            'light freezing rain' => WeatherType::LIGHT_RAIN,
            'clear' => WeatherType::STORMS,
            default => WeatherType::CLEAR 
        };

        return $weather->fill([
            "data" => $data,  //todo ensure forecasts/data etc are date checked

            "precipitation" => $data['current']['precip'],
            "temperature" => $data['current']['temperature'],
            "wind_speed" => $data['current']['wind_speed'],
            "uv_index" => $data['current']['uv_index'],
            "warning" => false,
            "type" => $type
        ]);
    }
}
