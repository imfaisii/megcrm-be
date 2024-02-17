<?php

use App\Fascade\AirCallFascade;
use App\Http\Controllers\AirCallWebhookController;
use App\Models\User;
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

Route::get('test-caller', function () {
  //   return  AirCallFascade::getUsers(); 
  //   return  AirCallFascade::getUsers(userId:1128951);   // hajra ben
  //   return  AirCallFascade::getCalls();   // hajra ben
  // return  AirCallFascade::searchCall([
  //   'user_id' => '1128951',
  //   'phone_number'=>'+447713176822'
  // ]);   // hajra ben

  // $data = AirCallFascade::getUsers();
  // // dd($data->getOriginalContent());
  // //  data_get($data->getOriginalContent(), 'datas', collect([]));

});

Route::get('/', function () {

  return ['Laravel' => app()->version()];
});

Route::get('/dropbox', function () {
    $redirect = "http://localhost:8000/dropbox/redirect";
    $url = "https://www.dropbox.com/oauth2/authorize?client_id=8vda4d31bbpfvxm&response_type=code&redirect_uri=$redirect&token_access_type=offline";

    return redirect($url);
});

Route::get('/dropbox/redirect', function () {
    dd(request()->all());
});

Route::prefix('aircall')->as('aircall_')->group(function () {
  Route::post('check/webhook', AirCallWebhookController::class)->name("webhook");
});
