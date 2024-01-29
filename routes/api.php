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


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return [
            'data' => [
                'user' => $request->user()
            ]
        ];
    });

    Route::get('/get-permissions', function () {
        return response()->json([
            'data' => json_decode(auth()->user()->jsPermissions())
        ]);
    });

    Route::apiResource('/permissions', PermissionController::class);

    Route::apiResource('/roles', RoleController::class);

    Route::apiResource('/users', UserController::class);

    Route::apiResource('/leads', LeadController::class);
    Route::get('/lead-extras', [LeadController::class, 'getExtras'])->name('leads.extras');
    Route::post('/lead-status/{lead}', [LeadController::class, 'updateStatus'])->name('leads.set-status');
});
