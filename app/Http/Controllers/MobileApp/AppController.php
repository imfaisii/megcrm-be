<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;

class AppController extends Controller
{
    public function getVersion($platform, $version)
    {
        $latestRelease = config('mobile-app.latest_version');

        $response = [
            "platform" => $platform,
            "latest_version" => $latestRelease,
            "current_version" => $version,
            "updateAvailable" => $latestRelease > $version,
        ];

        return $this->success(data: $response);
    }
}
