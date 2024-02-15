<?php

use App\Classes\AirCall;
use App\Models\User;
use App\Notifications\Events\NewCallScheduledNotification;
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
  
   
    // // userId:1128951
    // // return (new AirCall())->startACall(userId:1129004,queryParams:[
    // //     'number_id'=>'720272',
    // //     'to'=>'+447932460925'
    // // ]);   // samiya 
    //      return (new AirCall())->getUsers(userId:1129004);
    
    
});

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
