<?php

use App\Classes\GetAddress;
use App\Fascade\AirCallFascade;
use App\Http\Controllers\AirCallWebhookController;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

use function App\Helpers\formatCommas;
use function App\Helpers\removeSpace;

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


        $lastLead = Lead::latest()->first()->setAppends([]);
       $answ = $lastLead->update([
            'post_code'=>$lastLead->post_code,
        ]);
        dd($answ);

    //     dd("asfsaf");
        $postCodeAddress = $lastLead->only(['post_code','address']);
        $Other = Arr::except($lastLead->toArray(),['post_code','address']);
 return            $lead =  Lead::firstOrCreate([...$postCodeAddress,'address'=>'test, Test13'],$Other);


             $array2 = Lead::get(['post_code','address'])->transform(function($value){
                    return [
                        'post_code' => strtolower(removeSpace($value->post_code)),
                        'address' => strtolower(formatCommas($value->address)),
                    ];
    })->groupBy('post_code')->transform(function($value){
        return $value->pluck('address')->toArray();
    })->toArray();
        $array1 = [
            "E12 5AH" => ["Flat 1 Oldham House 3a Grantham Road London -- England"],
            "WA76JF" => ["57 Falmouth Place Murdishaw Runcorn Cheshire -- England"],
            "g521dl" => ["flat 0/1 32 ulva street glasgow -- scotland"],
            "B27 7aj" => ["330 Gospel Lane Birmingham West Midlands -- England"],
            "ol9 9sb" => ["7 Dakin Road Norwich Norfolk -- England", "test address 9n 90"]
        ];
        // $array2 = [
        //     "E12 5AH" => ["Flat 1 Oldham House 3a Grantham Road London -- England"],
        //     "WA76JF" => ["57 Falmouth Place Murdishaw Runcorn Cheshire -- England"],
        //     "G52 1DL" => ["Flat 0\/1 32 Ulva Street Glasgow -- Scotland"],
        //     "B27 7aj" => ["330 Gospel Lane Birmingham West Midlands -- England"],
        //     "ol9 9sc" => ["7 Dakin Road Norwich Norfolk -- England", "test address 9n 91"]
        // ];

        $diffKeys = Arr::map($array1, function ($val, $key) use ($array2) {
            return array_filter($val, function ($value) use ($array2, $key) {
                return !in_array($value, Arr::get($array2, $key, []));

            });
        });
            $allowedPostCodes = array_keys($diffKeys);
            $allwoedAddresses = Arr::flatten($diffKeys);
            dd($allowedPostCodes,$allwoedAddresses);
        (collect($diffKeys)->filter()->toArray());
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
