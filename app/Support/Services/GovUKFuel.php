<?php

namespace App\Support\Services;

use App\Models\Fuel;
use Illuminate\Support\Str;
use App\Traits\SensesUUID;
use App\Interfaces\Services\FuelService;

class GovUKFuel implements FuelService
{

    use SensesUUID;

    static function get()
    {
        $link = 'h'.Str::of(chop(file_get_contents('https://www.gov.uk/government/statistics/weekly-road-fuel-prices')))->after("\"name\": \"Weekly road fuel prices (CSV)\",")->before("\"encodingFormat\"")->between('h', 'v').'v';
        //dd($link);
        $rawCSVData = file($link);

        for ($i = sizeOf($rawCSVData) - 1; $i != 0; $i--) {
            $line = explode(',', chop($rawCSVData[$i]));
            if ($line[0] == 'Date') {
                break;
            } else {
                $date = date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', $line[0])));
                $recordCheck = Fuel::where('date', '=', $date)->get();
                if ($recordCheck->isEmpty() && $date != '1970-01-01 00:00:00') {
                    $fuel = Fuel::create(["date" => date("Y-m-d", strtotime(str_replace('/', '-', $line[0]))), "pump_price_petrol" => $line[1], "pump_price_diesel" => $line[2], "duty_price_petrol" => $line[3], "duty_price_diesel" => $line[4], "vat_percentage_petrol" => $line[5], "vat_percentage_diesel" => $line[6],]);
                    $fuel->save();
                } else {
                    break;
                }
            }
        };
    }
}
