<?php

use App\Enums\AppEnum;
use App\Http\Controllers\AirCallWebhookController;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\Customer\CustomerLeadTrackingMail;
use App\Notifications\TextExponentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

use function App\Helpers\meg_encrypt;

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

if (app()->isLocal()) {
    Route::get('test', function (Request $request) {

        $userId = request()->get('user_id', 1);
        $user = User::find($userId);

        $user->notify(new TextExponentNotification());

        dd('done');
    });

    Route::get('test-lead-track', function (Request $request) {

        $time = now()->addDays(AppEnum::LEAD_TRACKNG_DAYS_ALLOWED);
        $lead = Lead::first();
        $encryptedID = meg_encrypt($lead->id);
        $encryptedModel = meg_encrypt('Lead');
        $route = URL::temporarySignedRoute('customer.lead-status', $time, ['lead' => $encryptedID]);
        $request = Request::create($route);
        $routeForFiles = URL::temporarySignedRoute('file_upload', $time, ['ID' => $encryptedID, 'Model' => $encryptedModel]);
        $requestForFilesUpload = Request::create($routeForFiles);
        $requestForFilesDelete = Request::create(URL::temporarySignedRoute('file_delete', $time, ['ID' => $encryptedID, 'Model' => $encryptedModel]));
        $requestForFilesData = Request::create(URL::temporarySignedRoute('file_data', $time, ['ID' => $encryptedID, 'Model' => $encryptedModel]));

        $requestForFiles = Request::create($route);

        $lead->notify((new CustomerLeadTrackingMail([
            ...$request->query(),
            'lead' => $encryptedID,
            'model' => $encryptedModel,
            'SignatureForUpload' => $requestForFilesUpload->query('signature'),
            'SignatureForDelete' => $requestForFilesDelete->query('signature'),
            'SignatureForData' => $requestForFilesData->query('signature'),

        ])));

    });

}

Route::get('/', fn () => ['Laravel' => app()->version()]);
Route::get('/dropbox/redirect', fn () => response()->json(response()->all()));

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
