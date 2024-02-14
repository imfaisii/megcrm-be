<?php

use App\Enums\AppEnum;
use App\Models\User;
use App\Notifications\StatusChangeNotification;
use Illuminate\Support\Facades\Notification;
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

Route::get('test',function(){
    
    $user = User::skip(1)->take(1)->first();
    Notification::send($user,new StatusChangeNotification());
   return $data = $user->notify(new StatusChangeNotification());
    dd($data);

});

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
