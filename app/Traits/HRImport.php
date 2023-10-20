<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\User;

trait HRImport
{
    public function getUser($row)
    {
        $user = User::where('external_service_id', $row['Employee Id'])->first();
        if (!$user) {
            $this->info('User with an external_service_id of ' . $row['Employee Id'] . ' does not exist');
            return null;
        }
        return $user;
    }

    public function formatDate($date)
    {
        $carbonAcceptedDate = str_replace('/', '-', $date);

        $dateToReturn = Carbon::parse($carbonAcceptedDate);

        // Return carbon date
        return $dateToReturn;
    }

    public function getUserByName($fullName)
    {
        if (!isset($fullName) || empty($fullName)) {
            $this->info('Defaulted user to Senses System ID as user is empty');
            return getSensesSystemUser();
        }
        $createdBy = User::where('full_name', $fullName)->whereNotNull('external_service_id')->get();
        if ($createdBy->count() == 1) {
            return $createdBy->first();
        } elseif ($createdBy->count() > 1) {
            $this->info('Defaulted user to Senses System ID as there are multiple ' . $fullName . ' in the database');
            return getSensesSystemUser();
        } else {
            $this->info('Defaulted user to Senses System ID as ' . $fullName . ' does NOT exist in the database');
            return getSensesSystemUser();
        }
    }

    public function returnBoolean($datum, $nullable = false)
    { //converts data into a boolean, if you set nullable to true it'll return true when value is empty, otherwise it will return false when value is empty
        if (!isset($datum) || empty($datum)) {
            return $nullable ? null : false;
        }

        if (in_array($datum, ['Yes', 'y', 'YES', 'yes', true])) {
            return true;
        } else if (in_array($datum, ['No', 'n', 'NO', 'no', false])) {
            return false;
        }
    }

    public function sanitiseString($string)
    {
        return preg_replace('/\s/u', ' ', str_replace("\xc2\xa0", ' ', $string));
    }

    public function stripNbsp($string)
    {
        return !empty($string) ? str_replace('&nbsp;', '<br/>', $string) : null;
    }

    public function convertHoursAndMinutes($string)
    {
        $minutes = 0;
        if (strpos($string, ':') !== false) {
            // Split hours and minutes.
            list($hours, $minutes) = explode(':', $string);
        }
        return floatval($hours) * 60 + floatval($minutes);
    }

    public function getDuration(Carbon $startDate, Carbon $endDate)
    {
        if ($startDate->isSameDay($endDate)) {
            return 1;
        }

        return $startDate->diffInWeekdays($endDate);
    }
}
