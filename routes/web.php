<?php

use App\Classes\AirCall;
use App\Fascade\AirCallFascade;
use App\Http\Controllers\AirCallWebhookController;
use App\Models\User;
use App\Notifications\Events\NewCallScheduledNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {

    return ['Laravel' => app()->version()];
});

Route::prefix('aircall')->as('aircall_')->group(function () {
    Route::post('check/webhook', AirCallWebhookController::class)->name("webhook");
});
