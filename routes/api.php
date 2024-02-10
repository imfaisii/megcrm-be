<?php

use App\Http\Controllers\BenefitTypeController;
use App\Http\Controllers\CallCenterController;
use App\Http\Controllers\CallCenterStatusesController;
use App\Http\Controllers\FuelTypeController;
use App\Http\Controllers\JobTypeController;
use App\Http\Controllers\LeadGeneratorAssignmentController;
use App\Http\Controllers\LeadGeneratorController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Leads\StatusController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\MeasureController;
use App\Http\Controllers\Permissions\PermissionController;
use App\Http\Controllers\Permissions\RoleController;
use App\Http\Controllers\SurveyorController;
use App\Http\Controllers\Users\UserController;
use Carbon\Carbon;
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
    Route::apiResource('/lead-statuses', StatusController::class);
    Route::apiResource('/lead-generator-assignments', LeadGeneratorAssignmentController::class);

    Route::apiResource('/lead-generators', LeadGeneratorController::class);
    Route::apiResource('/lead-sources', LeadSourceController::class);
    Route::apiResource('/surveyors', SurveyorController::class);
    Route::apiResource('/job-types', JobTypeController::class);
    Route::apiResource('/benefit-types', BenefitTypeController::class);
    Route::apiResource('/fuel-types', FuelTypeController::class);
    Route::apiResource('/measures', MeasureController::class);
    Route::apiResource('/call-center', CallCenterController::class);
    Route::apiResource('/call-center-statuses', CallCenterStatusesController::class);

    Route::post('/leads/upload', [LeadController::class, 'handleFileUpload'])->name('leads.file-upload');

    Route::get('/lead-extras', [LeadController::class, 'getExtras'])->name('leads.extras');
    Route::post('/lead-status/{lead}', [LeadController::class, 'updateStatus'])->name('leads.set-lead-status');
});

Route::get('/calendar/events', function () {
    $data = [
        'id' => 1,
        'title' => 'Test Event',
        'start' => Carbon::now(),
        'end' => Carbon::now()->addHour(),
        'extendedProps' => [
            'calendar' => 'Holiday',
            'guests' => [],
            'location' => 'Dunkinfield',
            'description' => 'Some description'
        ],
        'allDay' => false
    ];

    $calendars = request()->get('calendars', '');

    $array = explode(",", $calendars);

    return response()->json([
        'data' => in_array('Holiday', $array) ? [$data] : []
    ]);
});
