<?php

use Aloha\Twilio\Twilio;
use App\Http\Controllers\AirCallWebhookController;
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

Route::get('test', function () {
    // throw new Exception('test');

    $client = new Twilio('ACbfa4b3596a5e63cca3e4dece3dd6a7a7', '99583c420d5aed08d3dc10b57e480c69', '447480822674');

    $client->message('447943111370', "Test Message from Umer Riaz");
});

Route::get('/', fn () => ['Laravel' => app()->version()]);
Route::get('/dropbox/redirect', fn () => response()->json(response()->all()));

Route::get('/dropbox', function () {
    $redirect = "http://localhost:8000/dropbox/redirect";
    $url = "https://www.dropbox.com/oauth2/authorize?client_id=8vda4d31bbpfvxm&response_type=code&redirect_uri=$redirect&token_access_type=offline";

    return redirect($url);
});


Route::get('/webhook/{name}', function ($name) {
    Log::driver($name)->info("Testing web hook");

    return response()->json('Done');
});

Route::prefix('aircall')->as('aircall_')->group(function () {
    Route::post('check/webhook', AirCallWebhookController::class)->name("webhook");
});
