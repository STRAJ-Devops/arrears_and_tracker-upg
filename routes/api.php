<?php

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\ArrearController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\MonitorController;
use App\Http\Controllers\WrittenOffController;
use Illuminate\Support\Facades\Route;

/**
 * Auth routes
 */
Route::controller(LoginController::class)->group(function () {
    Route::post('login', 'authenticate');
});

Route::post('customer-details', [App\Http\Controllers\API\CustomerController::class, 'customer']);
Route::get('online-customer-details', [App\Http\Controllers\API\CustomerController::class, 'online_customer']);
Route::post('group-details', [App\Http\Controllers\API\CustomerController::class, 'group']);
Route::post('written-of-customer-details', [App\Http\Controllers\API\WrittenOffController::class, 'customer']);
Route::get('online-written-of-customer-details', [App\Http\Controllers\API\WrittenOffController::class, 'online_customer']);
//upload template file
Route::post('daily-upload', [App\Http\Controllers\API\SaleController::class, 'fileUpload']);

Route::post('deploy', [App\Http\Controllers\DeploymentController::class, 'deploy']);

Route::get('dashboard', [DashboardController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);
    Route::get('incentives', [App\Http\Controllers\API\IncentiveController::class, 'calculateIncentive']);
    Route::post('arrears', [ArrearController::class, 'group_by']);
    Route::post('sales', [SaleController::class, 'group_by']);
    Route::post('expected', [App\Http\Controllers\ExpectedController::class, 'group_by']);
    Route::post('add-comment', [App\Http\Controllers\API\CommentController::class, 'store']);
    Route::get('get-all-comments', [App\Http\Controllers\API\CommentController::class, 'getComments']);
    Route::get('show-all-comments', [App\Http\Controllers\API\CommentController::class, 'index']);
    Route::get('create-monitor', [MonitorController::class, 'create']);
    Route::post('store-monitor', [MonitorController::class, 'store']);
    Route::get('edit-monitor/{id}', [MonitorController::class, 'edit']);
    Route::get('get-monitors', [MonitorController::class, 'getMonitors']);
    Route::post('appraise', [MonitorController::class, 'appraise']);
    Route::post('apply', [MonitorController::class, 'apply'])->name('apply');
    Route::post('get-expected-repayments', [App\Http\Controllers\ExpectedController::class, 'group_by']);
    Route::post('upload-written-off-customers', [WrittenOffController::class, 'importWrittenOffs']);
    Route::get('calendar', [App\Http\Controllers\API\CalendarController::class, 'getcalender']);
    Route::get('maturities', [App\Http\Controllers\API\MaturityLoanController::class, 'group_by']);
});
