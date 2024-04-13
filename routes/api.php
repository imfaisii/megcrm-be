<?php

use App\Classes\GetAddress;
use App\Http\Controllers\AirCall\AirCallController;
use App\Http\Controllers\BenefitTypeController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalenderEventsController;
use App\Http\Controllers\CallCenterController;
use App\Http\Controllers\CallCenterStatusesController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\File\FileHanlderController;
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
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SurveyorController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Users\UserController;
use App\Http\Requests\Leads\GetAddressRequest;
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

require __DIR__.'/auth.php';

Route::get('/getSuggestions', function (GetAddressRequest $request) {
    $getAddress = new GetAddress();

    try {
        return response()->json([
            'data' => $getAddress->getSuggestions($request->post_code),
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'data' => [],
        ]);
    }
});

Route::middleware('signed')->group(function () {
    Route::post('/file-upload/{Model}/{ID}', [FileHanlderController::class, 'upload'])->name('file_upload')->middleware('throttle:customer-file-upload');
    Route::post('/file-delete/{Model}/{ID}', [FileHanlderController::class, 'delete'])->middleware('throttle:customer-file-upload')->name('file_delete');
    Route::post('/file-data/{Model}/{ID}', [FileHanlderController::class, 'getAllFilesAssocaiatedWithModel'])->middleware('throttle:customer-file-upload')->name('file_data');
    Route::get('customer-lead-status-view/{lead}', [CustomerController::class, 'lead_view'])->name('customer.lead-status')->middleware('verify_domain');
    Route::get('/leads-links/council-tax/{postcode}', [LeadController::class, 'getCouncilTaxLink'])->name('leads.council-tax-link');

});
Route::get('/file-load/{Media:uuid}', [FileHanlderController::class, 'load'])->name('file_load');
Route::post('/file-download', [LeadController::class, 'getDataMatchFile'])->name('file_download');

Route::get('/leads-links/council-tax/{postcode}', [LeadController::class, 'getCouncilTaxLink'])->name('leads.council-tax-link');
Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/get-permissions', function () {
        return response()->json([
            'data' => json_decode(auth()->user()->jsPermissions()),
        ]);
    });

    Route::get('/getSuggestions', function (GetAddressRequest $request) {
        $getAddress = new GetAddress();

        try {
            return response()->json([
                'data' => $getAddress->getSuggestions($request->post_code),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ]);
        }
    });

    Route::get('/user', [UserController::class, 'currentUser']);
    Route::post('/users/{user}/collections/docs/upload', [UserController::class, 'uploadDocumentToCollection'])->name('users.documents-to-collections');
    Route::post('/users/{user}/documents/upload', [UserController::class, 'uploadDocument'])->name('users.documents-upload');
    Route::post('/users/{media}/expiry/update', [UserController::class, 'updateDocumentExpiry'])->name('users.update-document-expiry');
    Route::apiResource('/permissions', PermissionController::class);
    Route::apiResource('/roles', RoleController::class);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/companies', CompanyController::class);
    Route::post('/companies/{company}/collections/docs/upload', [CompanyController::class, 'uploadDocumentToCollection'])->name('companies.documents-to-collections');
    Route::post('/companies/{media}/expiry/update', [CompanyController::class, 'updateDocumentExpiry'])->name('companies.update-document-expiry');
    Route::put('/users/{user}/profile', [UserController::class, 'updateUserProfile'])->name('users.profile');
    Route::apiResource('/leads', LeadController::class);
    Route::post('/leads/{lead}/collections/docs/upload', [LeadController::class, 'uploadDocumentToCollection'])->name('leads.documents-to-collections');
    Route::post('/leads/{lead}/comments', [LeadController::class, 'storeComments'])->name('leads.add-comments');
    Route::get('/leads-datamatch-files', [LeadController::class, 'getDataMatchResultsFileLink'])->name('leads.view_data_match_files');
    Route::get('/leads/{lead}/mobile-asset/{assetId}', [LeadController::class, 'storeMobileAssetsId'])->name('leads.add-asset-ids');
    Route::get('/leads-datamatch-download', [LeadController::class, 'downloadDatamatch'])->name('leads.download-datamatch');
    Route::post('/leads-datamatch-upload', [LeadController::class, 'uploadDatamatch'])->name('leads.upload-datamatch');
    Route::get('/lead-jobs', [LeadJobController::class, 'index'])->name('lead-jobs.index');
    Route::post('/send-sms/{lead}', [SmsController::class, 'sendSmsToLead'])->name('leads.send-sms-to-lead');
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
    Route::apiResource('/team', TeamController::class);
});
