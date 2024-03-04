<?php

use App\Classes\GetAddress;
use App\Enums\DataMatch\DataMatchEnum;
use App\Exports\Leads\DatamatchExport;
use App\Fascade\AirCallFascade;
use App\Http\Controllers\AirCallWebhookController;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

use function App\Helpers\extractFirstNumericNumber;
use function App\Helpers\formatCommas;
use function App\Helpers\removeSpace;
use function App\Helpers\replaceFirst;

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
    Route::get('test', function () {
        $geeks = 'Welcome 2,1/2 Geeks 4.8 Geeks.';

        function cleanString($s) {
            // Remove all characters other than numbers, '.', ',', '/', and '-'
            $cleanedString = preg_replace('/[^0-9.,\/-]/', ' ', $s);

            // Remove extra spaces at the end
            $cleanedString = rtrim($cleanedString);

            return $cleanedString;
        }

        // Example usage
        $s = "The price is $12.50, and the quantity is 3.5 and 2,1/2.";
        return cleanString($geeks);  // Output: 12.50 3.5 2,1/2

        // Use preg_match_all() function to check match
        preg_match_all('/\b(\d+(?:\.\d+)?(?:\/\d+)?(?:-\d+)?)(?=[a-zA-Z ]|$)/', $geeks, $matches);



        

        return $matches;
        $lead = Lead::find(11);
        return extractFirstNumericNumber("flat 2.5");
      $answer =  $lead->sub_building ? extractFirstNumericNumber($lead->sub_building) : ($lead->building_number ? extractFirstNumericNumber($lead->building_number) : extractFirstNumericNumber($lead->addres));
        dd($lead->sub_building ? 'sub' : ($lead->building_number ? 'bulding' : 'nothging') );
      replaceFirst($lead->sub_building ? extractFirstNumericNumber($lead->sub_building) : ($lead->building_number ? extractFirstNumericNumber($lead->building_number) : extractFirstNumericNumber($lead->addres)), '', $lead->address);

        return new DatamatchExport;

    });
}


Route::get('/', fn() => ['Laravel' => app()->version()]);
Route::get('/dropbox/redirect', fn() => response()->json(response()->all()));

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
