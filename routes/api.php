<?php

use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Permissions\PermissionController;
use App\Http\Controllers\Permissions\RoleController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

require __DIR__ . '/auth.php';

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['as' => 'permissions.'], function () {
    Route::apiResource('/permissions', PermissionController::class);
});

Route::group(['as' => 'roles.'], function () {
    Route::apiResource('/roles', RoleController::class);
});

Route::group(['as' => 'users.'], function () {
    Route::apiResource('/users', UserController::class);
});

Route::group(['as' => 'leads.'], function () {
    Route::apiResource('/leads', LeadController::class);

    Route::get('/lead-extras', [LeadController::class, 'getExtras'])->name('extras');
});
