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

Route::post('customer-details', [App\Http\Controllers\API\CustomerController::class, 'customer'])->name('customer-details');
Route::post('group-details', [App\Http\Controllers\API\CustomerController::class, 'group'])->name('group-details');
Route::post('written-of-customer-details', [App\Http\Controllers\API\WrittenOffController::class, 'customer'])->name('group-details');
//upload template file
Route::post('daily-upload', [App\Http\Controllers\API\SaleController::class, 'fileUpload'])->name('upload-template');

Route::post('deploy', [App\Http\Controllers\DeploymentController::class, 'deploy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('incentives', [App\Http\Controllers\API\IncentiveController::class, 'calculateIncentive']);
    Route::post('arrears', [ArrearController::class, 'group_by'])->name('arrears-group-by');
    Route::post('sales', [SaleController::class, 'group_by'])->name('sales-group-by');
    Route::post('expected', [App\Http\Controllers\ExpectedController::class, 'group_by'])->name('expected-group-by');
    Route::post('add-comment', [App\Http\Controllers\API\CommentController::class, 'store'])->name('add-comment');
    Route::get('get-all-comments', [App\Http\Controllers\API\CommentController::class, 'getComments'])->name('allComments');
    Route::get('show-all-comments', [App\Http\Controllers\API\CommentController::class, 'index'])->name('showAllComments');
    Route::get('create-monitor', [MonitorController::class, 'create'])->name('create-monitor');
    Route::post('store-monitor', [MonitorController::class, 'store'])->name('store-monitor');
    Route::get('edit-monitor/{id}', [MonitorController::class, 'edit'])->name('edit-monitor');
    Route::get('get-monitors', [MonitorController::class, 'getMonitors'])->name('get-monitors');
    Route::post('appraise', [MonitorController::class, 'appraise'])->name('appraise');
    Route::post('apply', [MonitorController::class, 'apply'])->name('apply');
    Route::post('get-expected-repayments', [App\Http\Controllers\ExpectedController::class, 'group_by']);
    Route::post('upload-written-off-customers', [WrittenOffController::class, 'importWrittenOffs'])->name('upload-written-off-customers');
    Route::get('calendar', [App\Http\Controllers\API\CalendarController::class, 'getcalender'])->name('calendar');
    Route::get('maturities', [App\Http\Controllers\API\MaturityLoanController::class, 'group_by'])->name('maturities');
});
