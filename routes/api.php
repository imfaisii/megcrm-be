<?php

use App\Http\Controllers\AirCall\AirCallController;
use App\Http\Controllers\BenefitTypeController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalenderEventsController;
use App\Http\Controllers\CallCenterController;
use App\Http\Controllers\CallCenterStatusesController;
use App\Http\Controllers\FuelTypeController;
use App\Http\Controllers\InstallationTypeController;
use App\Http\Controllers\JobTypeController;
use App\Http\Controllers\LeadGeneratorAssignmentController;
use App\Http\Controllers\LeadGeneratorController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Leads\LeadJobController;
use App\Http\Controllers\Leads\StatusController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\MeasureController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Permissions\PermissionController;
use App\Http\Controllers\Permissions\RoleController;
use App\Http\Controllers\SurveyorController;
use App\Http\Controllers\Users\UserController;
use App\Http\Requests\Leads\GetAddressRequest;
use Illuminate\Support\Facades\Http;
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

Route::get('/getSuggestions', function (GetAddressRequest $request) {
    $token = config('app.get_address_api');

    try {
        $response = Http::asForm()->post("https://api.getAddress.io/autocomplete/{$request->post_code}?api-key=$token", [
            'all' => true,
            'template' => '{formatted_address} -- {country}'
        ]);

        return response()->json([
            'data' => $response->json()['suggestions']
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'data' => []
        ]);
    }
});

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/get-permissions', function () {
        return response()->json([
            'data' => json_decode(auth()->user()->jsPermissions())
        ]);
    });

    Route::get('/user', [UserController::class, 'currentUser']);

    Route::post('/users/{user}/documents/upload', [UserController::class, 'uploadDocument'])->name('users.documents-upload');
    Route::apiResource('/permissions', PermissionController::class);
    Route::apiResource('/roles', RoleController::class);
    Route::apiResource('/users', UserController::class);
    Route::put('/users/{user}/profile', [UserController::class, 'updateUserProfile'])->name('users.profile');
    Route::apiResource('/leads', LeadController::class);
    Route::post('/leads/{lead}/comments', [LeadController::class, 'storeComments'])->name('leads.add-comments');
    Route::get('/leads-datamatch-download', [LeadController::class, 'downloadDatamatch'])->name('leads.download-datamatch');
    Route::get('/lead-jobs', [LeadJobController::class, 'index'])->name('lead-jobs.index');
    Route::apiResource('/lead-statuses', StatusController::class);
    Route::apiResource('/lead-generator-assignments', LeadGeneratorAssignmentController::class);

    Route::apiResource('/lead-generators', LeadGeneratorController::class);
    Route::apiResource('/lead-sources', LeadSourceController::class);
    Route::apiResource('/surveyors', SurveyorController::class);
    Route::apiResource('/installation-types', InstallationTypeController::class);
    Route::apiResource('/job-types', JobTypeController::class);
    Route::apiResource('/benefit-types', BenefitTypeController::class);
    Route::apiResource('/fuel-types', FuelTypeController::class);
    Route::apiResource('/measures', MeasureController::class);
    Route::apiResource('/call-center', CallCenterController::class);
    Route::apiResource('/call-center-statuses', CallCenterStatusesController::class);
    Route::apiResource('/calendars', CalendarController::class);
    Route::apiResource('/calendar-events', CalenderEventsController::class);

    Route::post('aircall/search-call', [AirCallController::class, 'searchCall'])->name('aircall.search-call');
    Route::post('aircall/dial-call', [AirCallController::class, 'dialCall'])->name('aircall.dial-call');
    Route::post('aircall/make-call', [AirCallController::class, 'makeCall'])->name('aircall.make-call');



    Route::get('/notifications/{id}', [NotificationController::class, 'markSingleAsMarked'])->name('notifications.mark-single-as-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'deleteNotification'])->name('notifications.destroy');
    Route::get('/notifications', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    Route::post('/leads/upload', [LeadController::class, 'handleFileUpload'])->name('leads.file-upload');

    Route::get('/lead-extras', [LeadController::class, 'getExtras'])->name('leads.extras');
    Route::post('/lead-status/{lead}', [LeadController::class, 'updateStatus'])->name('leads.set-lead-status');
});
