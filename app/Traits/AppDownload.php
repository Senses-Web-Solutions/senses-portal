<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait AppDownload {

    public function getAppDownloadedAt($key = 'downloaded-at') {
        $downloadedAt = request()->header('downloaded-at'); //has to be kebab apparently
        $hasDate =  Carbon::canBeCreatedFromFormat($downloadedAt, 'Y-m-d H:i:s');
        if ($hasDate) {
            $downloadedAt = Carbon::createFromFormat('Y-m-d H:i:s', $downloadedAt);
        }

        return $hasDate ? $downloadedAt : null;
    }

}
