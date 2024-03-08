<?php

use App\Classes\GetAddress;
use App\Enums\DataMatch\DataMatchEnum;
use App\Exports\Leads\DatamatchExport;
use App\Fascade\AirCallFascade;
use App\Http\Controllers\AirCallWebhookController;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

use function App\Helpers\removeStringFromString;
use function App\Helpers\extractFirstNumericNumber;
use function App\Helpers\removeSpace;
use function App\Helpers\replaceFirst;
use function App\Helpers\getOnlyNumersFromString;



use Aloha\Twilio\Twilio;


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

});

Route::get('/', fn() => ['Laravel' => app()->version()]);
Route::get('/dropbox/redirect', fn() => response()->json(response()->all()));

Route::get('/dropbox', function () {
    $redirect = 'http://localhost:8000/dropbox/redirect';
    $url = "https://www.dropbox.com/oauth2/authorize?client_id=8vda4d31bbpfvxm&response_type=code&redirect_uri=$redirect&token_access_type=offline";

    return redirect($url);
});

Route::get('/webhook/{name}', function ($name) {
    Log::driver($name)->info('Testing web hook');

    return response()->json('Done');
});

Route::prefix('aircall')->as('aircall_')->group(function () {
    Route::post('check/webhook', AirCallWebhookController::class)->name('webhook');
});
