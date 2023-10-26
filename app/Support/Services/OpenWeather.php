<?php

namespace App\Support\Services;

use App\Enums\WeatherType;
use Carbon\Carbon;
use App\Models\Weather;
use App\Interfaces\Services\WeatherService;
use DateTime;
use Illuminate\Support\Facades\Http;
use Shapefile\Geometry\Point;

class OpenWeather implements WeatherService
{

    protected $callType;

    public function getFromLatLng(Weather $weather, float $lat, float $lng, DateTime $date = null): Weather
    {

        if ($date > now()) {
            //Forecast Data
            $this->callType = 'forecast';
            $response = Http::timeout(30)->get("https://api.openweathermap.org/data/2.5/onecall?units=metric&lat=" . $lat . "&lon=" . $lng . "&appid=" . env('OPENWEATHER_API_KEY'));
        }
        else if($date <= now()) {
            //Historical
            $this->callType = 'historical';
            $timestamp = Carbon::parse($date)->timestamp;
            $response = Http::timeout(30)->get("https://api.openweathermap.org/data/2.5/onecall/timemachine?units=metric&lat=" . $lat . "&lon=" . $lng . "&dt=" . $timestamp . "&appid=" . env('OPENWEATHER_API_KEY'));
        }
        $data = $response->json();
        if ($response->successful() && !isset($data['code'])) {

            $dayToForecast = $date->setTime(12, 00, 00);
            return $this->transformOpenWeather($weather, $data, $this->callType, Carbon::parse($dayToForecast)->timestamp);
        }

        return $weather;
        //return $this->transformWStack($weather, $data);
    }

    public function getFromGeom(Weather $weather, Point $geom, DateTime $date = null): Weather
    {

        $lat = $geom->getY();
        $lng = $geom->getX();

        return $this->getFromLatLng($weather, $lat, $lng, date: $date);
    }

    static function transformOpenWeather(Weather $weather, array $data, string $callType, $timestamp): Weather
    {

        $type = null;
        if ($callType == 'historical') {
            if (isset($data['current']['weather'][0])) {
                $type = $data['current']['weather'][0]['id'];
            }
        } else if ($callType == 'forecast') {
            $forecastCollection = collect($data['daily'])->keyBy('dt');

            $daysForecast = $forecastCollection[strval($timestamp)];
            if (isset($daysForecast['weather'][0])) {
                $type = $daysForecast['weather'][0]['id'];
            }
        }

        $type = match ($type) {
            800 => WeatherType::CLEAR,
            801 => WeatherType::PARTLY_CLOUDY,
            802 => WeatherType::PARTLY_CLOUDY,
            803 => WeatherType::CLOUDY,
            804 => WeatherType::OVERCAST,
            701 => WeatherType::MIST,
            711 => WeatherType::MIST,
            721 => WeatherType::MIST,
            731 => WeatherType::MIST,
            741 => WeatherType::FOG,
            751 => WeatherType::FOG,
            761 => WeatherType::FOG,
            762 => WeatherType::MIST,
            771 => WeatherType::MIST,
            781 => WeatherType::MIST,
            600 => WeatherType::LIGHT_SNOW,
            601 => WeatherType::SNOW,
            602 => WeatherType::HEAVY_SNOW,
            611 => WeatherType::SLEET,
            612 => WeatherType::LIGHT_SLEET,
            613 => WeatherType::LIGHT_SLEET,
            615 => WeatherType::LIGHT_SLEET,
            616 => WeatherType::SLEET,
            620 => WeatherType::HEAVY_SLEET,
            621 => WeatherType::HEAVY_SLEET,
            622 => WeatherType::HEAVY_SLEET,
            500 => WeatherType::LIGHT_RAIN,
            501 => WeatherType::RAIN,
            502 => WeatherType::HEAVY_RAIN,
            503 => WeatherType::HEAVY_RAIN,
            504 => WeatherType::HEAVY_RAIN,
            511 => WeatherType::HEAVY_RAIN,
            520 => WeatherType::LIGHT_RAIN,
            521 => WeatherType::RAIN,
            522 => WeatherType::HEAVY_RAIN,
            531 => WeatherType::HEAVY_RAIN,
            300 => WeatherType::LIGHT_RAIN,
            301 => WeatherType::RAIN,
            302 => WeatherType::HEAVY_RAIN,
            310 => WeatherType::LIGHT_RAIN,
            311 => WeatherType::RAIN,
            312 => WeatherType::HEAVY_RAIN,
            313 => WeatherType::RAIN,
            314 => WeatherType::HEAVY_RAIN,
            321 => WeatherType::RAIN,
            200 => WeatherType::STORMS,
            201 => WeatherType::STORMS,
            202 => WeatherType::STORMS,
            210 => WeatherType::STORMS,
            211 => WeatherType::STORMS,
            212 => WeatherType::STORMS,
            221 => WeatherType::STORMS,
            230 => WeatherType::STORMS,
            231 => WeatherType::STORMS,
            232 => WeatherType::STORMS,
            default => WeatherType::CLEAR
        };

        if ($callType == 'forecast') {

            $forecastCollection = collect($data['daily'])->keyBy('dt');

            $daysForecast = $forecastCollection[strval($timestamp)];

            $processedWeatherData = array (
                "timestamp" => $daysForecast['dt'],
                "sunrise" => $daysForecast['sunrise'],
                "sunset" => $daysForecast['sunset'],
                "temperature" => $daysForecast['temp']['day'],
                "feels_like" => $daysForecast['feels_like']['day'],
                "humidity" => $daysForecast['humidity'],
                "uv_index" => round($daysForecast['uvi']),
                "wind_speed" => $daysForecast['wind_speed'],
                "precipitation" => isset($daysForecast['rain']) ? $daysForecast['rain'] : 0.0,
                "snowfall" => isset($daysForecast['snow']) ? $daysForecast['snow'] : 0.0,
                "type" => $type,
                "cloud_cover" => $daysForecast['clouds']
            );

            return $weather->fill([
                "forecast_data" => $processedWeatherData,
                "warning" => false,
                "type" => $type
            ]);
        } else if ($callType == 'historical') {

            $processedWeatherData = array (
                "timestamp" => $data['current']['dt'],
                "sunrise" => $data['current']['sunrise'],
                "sunset" => $data['current']['sunset'],
                "temperature" => $data['current']['temp'],
                "feels_like" => $data['current']['feels_like'],
                "humidity" => $data['current']['humidity'],
                "uv_index" => round($data['current']['uvi']),
                "wind_speed" => $data['current']['wind_speed'],
                "precipitation" => isset($data['current']['rain']) ? $data['current']['rain']['1h'] : 0.0,
                "snowfall" => isset($data['current']['snow']) ? $data['current']['rain']['1h'] : 0.0,
                "type" => $type,
                "cloud_cover" => $data['current']['clouds']
            );

            return $weather->fill([
                "historical_data" => $processedWeatherData,
                "warning" => false,
                "type" => $type
            ]);
        }
    }
}
