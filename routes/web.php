<?php

use App\Http\Controllers\AirCallWebhookController;
use App\Models\User;
use App\Notifications\TextExponentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Classes\LeadResponseClass;
use Aloha\Twilio\Twilio;
use App\Actions\Leads\GetOtherSitesLinkAction;
use App\Enums\AppEnum;
use App\Http\Requests\TestRequest;
use App\Imports\Leads\LeadsImport;
use App\Imports\testImport;
use App\Jobs\GetEpcScrappedDataOfLead;
use App\Mail\TestEmail;
use App\Models\DataMatchFile;
use App\Models\Lead;
use App\Notifications\Customer\CustomerLeadTrackingMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use function App\Helpers\removeStringFromString;
use function App\Helpers\extractFirstNumericNumber;
use function App\Helpers\removeSpace;
use function App\Helpers\replaceFirst;
use function App\Helpers\getOnlyNumersFromString;
use Illuminate\Support\Str;
use function App\Helpers\meg_encrypt;

use PhpOffice\PhpSpreadsheet\IOFactory;


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

Route::get('test-x', function (Request $request) {
    dd(Carbon::now());
});

Route::get('test-epc/{lead}', function (Request $request, Lead $lead) {
    (new GetOtherSitesLinkAction())->getEpcDetails($lead);
});

Route::get('test-email', function (Request $request) {
    Mail::to("cfaisal009@gmail.com")->send(new TestEmail());

    dd("done");
});

Route::get('test-notifications', function (Request $request) {
    $userId = request()->get('user_id', 1);
    $user = User::find($userId);

    $user->notify(new TextExponentNotification());

    dd("done");
});

if (app()->isLocal()) {
    Route::get('test', function (Request $request) {



        $userId = request()->get('user_id', 1);
        $user = User::find($userId);

        $user->notify(new TextExponentNotification());

        dd("done");
    });


    Route::get('test-lead-track', function (Request $request) {

        $time = now()->addDays(AppEnum::LEAD_TRACKNG_DAYS_ALLOWED);
        $lead = Lead::where('email', 'haamzaaay@gmail.com')->first();
        if (blank($lead)) {
            return null;
        }
        $encryptedID = meg_encrypt($lead->id);
        $encryptedModel = meg_encrypt('Lead');
        $route = URL::temporarySignedRoute('customer.lead-status', $time, ['lead' => $encryptedID]);
        $request = Request::create($route);
        $routeForFiles = URL::temporarySignedRoute('file_upload', $time, ['ID' => $encryptedID, 'Model' => $encryptedModel]);
        $requestForFilesUpload = Request::create($routeForFiles);
        $requestForFilesDelete = Request::create(URL::temporarySignedRoute('file_delete', $time, ['ID' => $encryptedID, 'Model' => $encryptedModel]));

        $requestForFilesData = Request::create(URL::temporarySignedRoute('file_data', $time, ['ID' => $encryptedID, 'Model' => $encryptedModel]));
        $requestForSupport = Request::create(URL::temporarySignedRoute('customer.support-email', $time, ['ID' => $encryptedID]));

        $requestForFiles = Request::create($route);
        try {
            $lead->notify((new CustomerLeadTrackingMail([
                ...$request->query(),
                'lead' => $encryptedID,
                'model' => $encryptedModel,
                'SignatureForUpload' => $requestForFilesUpload->query('signature'),
                'SignatureForDelete' => $requestForFilesDelete->query('signature'),
                'SignatureForData' => $requestForFilesData->query('signature'),
                'SignatureForSupport' => $requestForSupport->query('signature'),

            ])));
        } catch (Exception $e) {
            dd($e->getMessage());
        }
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
