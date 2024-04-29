<?php

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\ArrearController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\IncentiveController;
use App\Http\Controllers\API\SaleController;
use Illuminate\Support\Facades\Route;

/**
 * Auth routes
 */
Route::controller(LoginController::class)->group(function () {
    Route::post('login', 'authenticate');
});

Route::middleware('auth:sanctum')->group(function () {
    /**
     * Auth routes
     */
    Route::post('logout', [LoginController::class, 'logout']);

    //dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    //incentives
    Route::get('incentives', [IncentiveController::class, 'calculateIncentive']);

    //arrears
    Route::post('arrears', [ArrearController::class, 'group_by'])->name('arrears-group-by');
    //sales
    Route::post('sales', [SaleController::class, 'group_by'])->name('sales-group-by');

    //expected
    Route::post('expected', [App\Http\Controllers\API\ExpectedController::class, 'group_by'])->name('expected-group-by');

    //comments
    Route::post('add-comment', [CommentController::class, 'store'])->name('add-comment');
    Route::get('get-all-comments', [CommentController::class, 'getComments'])->name('allComments');
    Route::get('show-all-comments', [CommentController::class, 'showAllComments'])->name('showAllComments');
});
