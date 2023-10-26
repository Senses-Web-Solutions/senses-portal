<?php

namespace App\Support\Services;

use App\Enums\WeatherType;
use Carbon\Carbon;
use App\Models\Weather;
use Illuminate\Support\Str;
use Shapefile\Geometry\Geometry;
use Shapefile\Geometry\Point;
use App\Interfaces\Services\WeatherService;
use DateTime;
use Illuminate\Support\Facades\Http;

class OpenMeteo implements WeatherService
{

    protected $callType;

    public function getFromLatLng(Weather $weather, float $lat, float $lng, DateTime $date = null): Weather
    {
        if (!$date) {
            return $weather;
        }

        $carbonDate = Carbon::instance($date);
        $dateString = $carbonDate->toDateString(); // for api call start_date & end_date

        $this->callType = 'unavailable';

        if ($date > now()) {
            $this->callType = 'forecast';
        } else {
            $this->callType = 'historical';
        }

        $weather->fill([
            "warning" => false,
            "type" => $this->callType
        ]);

        if ($this->callType == 'unavailable') {
            return $weather;
        }

        // API Route
        $response = Http::timeout(30)->withOptions(["verify"=>false])->get("https://api.open-meteo.com/v1/forecast?latitude=" . $lat . "&longitude=" . $lng . "&hourly=temperature_2m,relativehumidity_2m,dewpoint_2m,apparent_temperature,precipitation,rain,showers,snowfall,snow_depth,freezinglevel_height,weathercode,surface_pressure,cloudcover,windspeed_10m,winddirection_10m,windgusts_10m,temperature_80m,soil_temperature_0cm,soil_temperature_54cm,soil_moisture_0_1cm,soil_moisture_27_81cm,shortwave_radiation_instant,direct_radiation_instant,diffuse_radiation_instant,direct_normal_irradiance_instant,terrestrial_radiation_instant&daily=weathercode,temperature_2m_max,temperature_2m_min,apparent_temperature_max,apparent_temperature_min,sunrise,sunset,precipitation_sum,rain_sum,showers_sum,snowfall_sum,precipitation_hours,windspeed_10m_max,windgusts_10m_max,winddirection_10m_dominant&windspeed_unit=mph&start_date=" . $dateString . "&end_date=" . $dateString . "&timezone=GB");
        $data = $response->json();
        $dateTimeString = null;

        if ($response->successful()) {
            // The api returns 24 sets of weather data for each hour.
            // By getting the hour index, the assignment group will get the weather
            // for the hour of their shift

            $dateTimeString = $carbonDate->format('Y-m-d\TH:00');
            $hourIndex = array_search($dateTimeString, $data['hourly']['time']);

            $this->transformOpenMeteo($weather, $data, $this->callType, $hourIndex);
        }

        return $weather;
    }

    public function getFromGeom(Weather $weather, Point $geom, DateTime $date = null): Weather
    {
        $lat = $geom->getY();
        $lng = $geom->getX();

        return $this->getFromLatLng($weather, $lat, $lng, date: $date);
    }

    static function transformOpenMeteo(Weather $weather, array $data, string $callType, int $hourIndex): Weather
    {
        $type = $data['hourly']['weathercode'][$hourIndex];

        $type = match ($type) {
            0 => WeatherType::CLEAR,
            1 => WeatherType::CLEAR,
            2 => WeatherType::PARTLY_CLOUDY,
            3 => WeatherType::OVERCAST,
            45 => WeatherType::FOG,
            48 => WeatherType::FOG,
            51 => WeatherType::LIGHT_RAIN,
            53 => WeatherType::RAIN,
            55 => WeatherType::HEAVY_RAIN,
            56 => WeatherType::SLEET,
            57 => WeatherType::HEAVY_SLEET,
            61 => WeatherType::LIGHT_RAIN,
            63 => WeatherType::RAIN,
            65 => WeatherType::HEAVY_RAIN,
            66 => WeatherType::SLEET,
            67 => WeatherType::HEAVY_SLEET,
            71 => WeatherType::LIGHT_SNOW,
            73 => WeatherType::SNOW,
            75 => WeatherType::HEAVY_SNOW,
            77 => WeatherType::SNOW,
            80 => WeatherType::LIGHT_RAIN,
            81 => WeatherType::RAIN,
            82 => WeatherType::HEAVY_RAIN,
            85 => WeatherType::LIGHT_SNOW,
            86 => WeatherType::HEAVY_SNOW,
            95 => WeatherType::STORMS,
            96 => WeatherType::STORMS,
            99 => WeatherType::STORMS,
            default => null,
        };

        $windDirectionIcon = null;
        $windDirection = $data['hourly']['winddirection_10m'][$hourIndex];

        if ($windDirection <= 22.5 || $windDirection >= 337.5) {
            // Up === 0 || 337.5 - 22.5
            $windDirectionIcon = 'arrow-up';
        } elseif ($windDirection > 22.5 && $windDirection <= 67.5) {
            // Up Right === 45 || 22.5 - 67.5
            $windDirectionIcon = 'arrow-up-right';
        } elseif ($windDirection > 67.5 && $windDirection <= 112.5) {
            // Right === 90 || 67.5 - 112.5
            $windDirectionIcon = 'arrow-right';
        } elseif ($windDirection > 112.5 && $windDirection <= 157.5) {
            // Down Left === 135 || 112.5 - 157.5
            $windDirectionIcon = 'arrow-down-right';
        } elseif ($windDirection > 157.5 && $windDirection <= 202.5) {
            // Down === 180 || 157.5 - 202.5
            $windDirectionIcon = 'arrow-down';
        } elseif ($windDirection > 202.5 && $windDirection <= 247.5) {
            // Down Left === 225 || 202.5 - 247.5
            $windDirectionIcon = 'arrow-down-left';
        } elseif ($windDirection > 247.5 && $windDirection <= 292.5) {
            // Left === 270 || 247.5 - 292.5
            $windDirectionIcon = 'arrow-left';
        } elseif ($windDirection > 292.5 && $windDirection < 337.5) {
            // Up Left === 315 || 292.5 - 337.5
            $windDirectionIcon = 'arrow-up-left';
        }

        $processedWeatherData = array (
            "timestamp" => $data['hourly']['time'][$hourIndex],
            "sunrise" => $data['daily']['sunrise'][0],
            "sunset" => $data['daily']['sunset'][0],
            "temperature_units" => $data['hourly_units']['temperature_2m'],
            "temperature" => round($data['hourly']['temperature_2m'][$hourIndex]),
            "feels_like" => $data['hourly']['apparent_temperature'][$hourIndex],
            "humidity" => $data['hourly']['relativehumidity_2m'][$hourIndex],
            "direct_radiation" => round($data['hourly']['direct_radiation_instant'][$hourIndex]),
            "wind_units" => $data['hourly_units']['windspeed_10m'],
            "wind_speed" => $data['hourly']['windspeed_10m'][$hourIndex],
            "wind_gust" => $data['hourly']['windgusts_10m'][$hourIndex],
            "wind_direction" => $windDirection,
            "wind_direction_icon" => $windDirectionIcon,
            "cloud_cover" => $data['hourly']['cloudcover'][$hourIndex],
            "precipitation" => $data['hourly']['precipitation'][$hourIndex],
            "precipitation_units" => $data['hourly_units']['precipitation'],
            "snow_fall" => $data['hourly']['snowfall'][$hourIndex],
            "snow_fall_units" => $data['hourly_units']['snowfall'],
            "snow_depth_units" => $data['hourly_units']['snow_depth'],
            "snow_depth" => $data['hourly']['snow_depth'][$hourIndex],
            "soil_temperature_surface" => $data['hourly']['soil_temperature_0cm'][$hourIndex],
            "soil_temperature_54cm" => $data['hourly']['soil_temperature_0cm'][$hourIndex],
            "soil_moisture_units" => $data['hourly_units']['soil_moisture_0_1cm'],
            "soil_moisture_surface" => $data['hourly']['soil_moisture_0_1cm'][$hourIndex],
            "soil_moisture_27_81cm" => $data['hourly']['soil_moisture_27_81cm'][$hourIndex],
            "type" => $type,
        );

        $weather->precipitation = $data['hourly']['precipitation'][$hourIndex];
        $weather->temperature = $data['hourly']['temperature_2m'][$hourIndex];
        $weather->wind_speed = $data['hourly']['windspeed_10m'][$hourIndex];

        return $weather->fill([
            $callType."_data" => $processedWeatherData,
        ]);
    }
}
