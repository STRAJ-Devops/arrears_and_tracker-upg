<?php

use App\Http\Controllers\ArrearController;
use App\Http\Controllers\BranchTargetController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProductTargetController;
use App\Http\Controllers\SaleController;
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
Route::get('sales-group-by', [SaleController::class, 'group_by'])->name('sales-group-by');

Route::post('upload-sales-targets', [SaleController::class, 'import'])->name('upload-sales-targets');
Route::post('upload-branch-targets', [BranchTargetController::class, 'import'])->name('upload-targets');
Route::post('upload-product-targets', [ProductTargetController::class, 'import'])->name('upload-product-targets');
Route::get('process-csv-sales', [SaleController::class, 'process_csv_for_sales'])->name('process-csv-sales');
Route::get('process-csv-arrears', [SaleController::class, 'process_csv_for_arrears'])->name('process-csv-arrears');
Route::post('arrears-group-by', [ArrearController::class, 'group_by'])->name('arrears-group-by');
Route::get('get-all-comments', [CommentController::class, 'getComments'])->name('allComments');



