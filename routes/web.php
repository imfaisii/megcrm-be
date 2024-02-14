<?php

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
    $user = User::skip(1)->take(1)->first();

    $user->notify(new NewCallScheduledNotification([
        'title' => 'Some title',
        'subtitle' => 'Some Subtitle',
        'module' => 'leads'
    ]));
});

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
