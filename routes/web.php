<?php

use App\Http\Controllers\ArrearController;
use App\Http\Controllers\ArrearsAndSalesController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchTargetController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTargetController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\TargetsUploaderController;
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

Route::group(['middleware' => 'auth:officer'], function () {

    Route::get('/', [HomeController::class, 'home']);
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('branches', [BranchController::class, 'index'])->name('branches');

    Route::get('user-management', [OfficerController::class, 'index'])->name('user-management');

    Route::get('products', [ProductController::class, 'index'])->name('products');

    Route::get('targets', [TargetController::class, 'index'])->name('targets');

    Route::get('arrears-and-sales-uploader', [ArrearsAndSalesController::class, 'index'])->name('arrears-and-sales-uploader');

    Route::get('regions', [RegionController::class, 'index'])->name('regions');

    Route::get('arrears', [ArrearController::class, 'index'])->name('arrears');

    Route::get('branch-targets-uploader', [BranchTargetController::class, 'index'])->name('branch-targets-uploader');
    Route::get('product-targets-uploader', [ProductTargetController::class, 'index'])->name('product-targets-uploader');

    Route::get('incentives', [IncentiveController::class, 'index'])->name('incentives');

    Route::get('tracker', [SaleController::class, 'index'])->name('tracker');

    Route::get('/logout', [SessionsController::class, 'destroy']);

    Route::post('/user-profile', [InfoUserController::class, 'store']);

    Route::post('arrears-group-by', [ArrearController::class, 'group_by'])->name('arrears-group-by');
    Route::post('sales-group-by', [SaleController::class, 'group_by'])->name('sales-group-by');

    Route::post('upload-branch-targets', [BranchTargetController::class, 'import'])->name('upload-targets');
    Route::post('upload-product-targets', [ProductTargetController::class, 'import'])->name('upload-product-targets');
    Route::post('upload-sales-targets', [SaleController::class, 'import'])->name('upload-sales-targets');

    Route::post('add-comment', [CommentController::class, 'store'])->name('add-comment');
    Route::get('comments', [CommentController::class, 'index'])->name('comments');
    Route::get('get-all-comments', [CommentController::class, 'getComments'])->name('allComments');
    Route::get('show-all-comments', [CommentController::class, 'showAllComments'])->name('showAllComments');

    Route::get('/login', function () {
        return view('dashboard');
    })->name('sign-up');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
    Route::get('/login/forgot-password', [ResetController::class, 'create']);
    Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');
