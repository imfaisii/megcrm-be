<?php

use App\Http\Controllers\MobileApp\AppController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/app',
    'as' => 'mobile-app.'
], function () {
    Route::get('/{platform}/versions/{version}/check-update', [AppController::class, 'getVersion']);
});
