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
use App\Notifications\SendDataMatchResultsNotification;
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
use AshAllenDesign\ShortURL\Classes\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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




    $data = [
        0 => [
            "datamatch_progress" => "Unverified",
            "datamatch_progress_date" => "2024-05-08",
            "dob" => "1996-07-15",
            "urn" => 6514653951,
            "address" => "Roxby Gardens",
            "post_code" => "YO12 4JN",
            "data_match_sent_date" => "2024-05-08",
            "first_name" => "Imogen",
            "last_name" => "Parker",
            "middle_name" => null,
            "leadGen_email" => "info@ecoboilergrants.uk",
            "leadGen_phone_no" => "7443011226",
            "leadGen_name" => "Saqi",

        ]
    ];
    $arr = [];
    $arr[] = "asf";
    $arr[] = "asfsaf";

    Notification::route('mail', [
        'barrett@example.com' => 'Barrett Blair',
    ])
        ->route('twilio', '+447443011226')
        ->notify(new SendDataMatchResultsNotification($data));

    return (new SendDataMatchResultsNotification($data))
        ->toMail(User::first());

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
        $lead->sendStatusEmailToCustomer(true);
    });
}

Route::get('/', fn() => ['Laravel' => app()->version()]);
Route::get('/dropbox/redirect', fn() => response()->json(request()->all()));

Route::get('/dropbox', function () {
    $redirect = 'http://localhost:8000/dropbox/redirect';
    $url = "https://www.dropbox.com/oauth2/authorize?client_id=s4otvomcy6a37lu&response_type=code&token_access_type=offline";

    return redirect($url);
});

Route::get('/webhook/{name}', function ($name) {
    Log::driver($name)->info('Testing web hook');

    return response()->json('Done');
});

Route::prefix('aircall')->as('aircall_')->group(function () {
    Route::post('check/webhook', AirCallWebhookController::class)->name('webhook');
});
