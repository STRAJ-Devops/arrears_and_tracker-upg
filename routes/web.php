<?php

use App\Http\Controllers\ArrearController;
use App\Http\Controllers\ArrearsAndSalesController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchTargetController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpectedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\PreviousEndMonthSalesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTargetController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\TargetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\OfficerTargetController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WrittenOffController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\MaturityLoanController;

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
Route::middleware('revalidate','auth:officer')->group(function () {
    Route::get('/', [HomeController::class, 'home']);
    Route::get('/loan-calculator', function () {
        return view('calculator')->with('title', 'Loan Calculator');
    })->name('loan-calculator');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('branches', [BranchController::class, 'index'])->name('branches');
    Route::get('user-management', [OfficerController::class, 'index'])->name('user-management');
    Route::get('create-user', [OfficerController::class, 'create'])->name('create-user');
    Route::post('store-user', [OfficerController::class, 'store'])->name('store-user');
    Route::get('edit-user/{id}', [OfficerController::class, 'edit'])->name('edit-user');
    Route::patch('update-user/{id}', [OfficerController::class, 'update'])->name('update-user');
    Route::delete('delete-user/{id}', [OfficerController::class, 'destroy'])->name('delete-user');
    Route::resource('roles', RoleController::class);
    Route::get('edit-role/{id}', [RoleController::class, 'edit'])->name('edit-role');
    Route::patch('update-role/{id}', [RoleController::class, 'update'])->name('update-role');
    Route::get('account-balance', function(){
        return view('account-balance');
    });
    Route::get('group-details', function(){
        return view('group-details');
    });
    Route::get('products', [ProductController::class, 'index'])->name('products');

    Route::get('targets', [TargetController::class, 'index'])->name('targets');

    Route::get('arrears-and-sales-uploader', [ArrearsAndSalesController::class, 'index'])->name('arrears-and-sales-uploader');

    Route::get('previous-end-month-sales-uploader', [PreviousEndMonthSalesController::class, 'index'])->name('previous-end-month-sales-uploader');

    Route::get('written-off-customers-uploader', [WrittenOffController::class, 'writtenOffUploader'])->name('written-off-customers-uploader');


    Route::get('regions', [RegionController::class, 'index'])->name('regions');

    Route::get('arrears', [ArrearController::class, 'index'])->name('arrears');

    Route::get('maturity-loans', [MaturityLoanController::class, 'index'])->name('maturity-loans');


    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::post('calender', [CalendarController::class, 'getcalender']);

    Route::get('branch-targets-uploader', [BranchTargetController::class, 'index'])->name('branch-targets-uploader');
    Route::get('upload-branch-targets', [BranchTargetController::class, 'uploadBranchTargets']);
    Route::get('delete-branch-targets', [BranchTargetController::class, 'deleteBranchTargets']);

    Route::get('officer-targets-uploader', [OfficerTargetController::class, 'index'])->name('officer-targets-uploader');
    Route::get('upload-officer-targets', [OfficerTargetController::class, 'uploadOfficerTargets']);
    Route::get('delete-officer-targets', [OfficerTargetController::class, 'deleteOfficerTargets']);

    Route::get('product-targets-uploader', [ProductTargetController::class, 'index'])->name('product-targets-uploader');
    Route::get('upload-product-targets', [ProductTargetController::class, 'uploadProductTargets']);
    Route::get('delete-product-targets', [ProductTargetController::class, 'deleteProductTargets']);

    Route::get('expected-repayments', [ExpectedController::class, 'index'])->name('expected-repayments');
    Route::post('get-expected-repayments', [ExpectedController::class, 'group_by']);

    Route::get('incentives', [IncentiveController::class, 'index'])->name('incentives');
    Route::get('incentives-settings', [IncentiveController::class, 'settings'])->name('incentives-settings');
    Route::patch('incentives-settings-update', [IncentiveController::class, 'update_incentive_settings'])->name('incentive-settings.store');
    Route::get('get-incentives', [IncentiveController::class, 'calculateIncentive']);

    Route::get('tracker', [SaleController::class, 'index'])->name('tracker');

    Route::get('/logout', [SessionsController::class, 'destroy']);

    Route::post('/user-profile', [InfoUserController::class, 'store']);

    Route::post('arrears-group-by', [ArrearController::class, 'group_by'])->name('arrears-group-by');
    Route::post('maturity-loans-group-by', [MaturityLoanController::class, 'group_by'])->name('maturity-loans-group-by');
    Route::post('sales-group-by', [SaleController::class, 'group_by'])->name('sales-group-by');

    Route::post('upload-branch-targets', [BranchTargetController::class, 'import'])->name('upload-targets');
    Route::post('upload-officer-targets', [OfficerTargetController::class, 'import'])->name('upload-officer-targets');
    Route::post('upload-product-targets', [ProductTargetController::class, 'import'])->name('upload-product-targets');
    Route::post('upload-sales-targets', [SaleController::class, 'import'])->name('upload-sales-targets');
    Route::post('upload-previous-end-month-sales', [SaleController::class, 'importPreviousEndMonthSales'])->name('upload-end-month-sales');
    Route::post('upload-written-off-customers', [WrittenOffController::class, 'importWrittenOffs'])->name('upload-written-off-customers');

    Route::post('add-comment', [CommentController::class, 'store'])->name('add-comment');
    Route::get('get-all-comments', [CommentController::class, 'getComments'])->name('allComments');
    Route::get('show-all-comments', [CommentController::class, 'showAllComments'])->name('showAllComments');
    Route::get('comments', [CommentController::class, 'index'])->name('comments');

    Route::get('monitors', [MonitorController::class, 'index'])->name('monitors');
    Route::get('create-monitor', [MonitorController::class, 'create'])->name('create-monitor');
    Route::post('store-monitor', [MonitorController::class, 'store'])->name('store-monitor');
    Route::get('edit-monitor/{id}', [MonitorController::class, 'edit'])->name('edit-monitor');
    Route::get('get-monitors', [MonitorController::class, 'getMonitors'])->name('get-monitors');
    // view monitor details
    Route::get('monitor-details/{id}', [MonitorController::class, 'show'])->name('monitor-details');
    Route::post('appraise', [MonitorController::class, 'appraise'])->name('appraise');
    Route::post('apply', [MonitorController::class, 'apply'])->name('apply');
    // add-activity comment
    Route::post('add-activity-comment', [MonitorController::class, 'add_comment'])->name('add-activity-comment');

    Route::get('customer-details', [CustomerController::class, 'customer'])->name('customer-details');
    Route::get('get-group-details', [CustomerController::class, 'group'])->name('customer-details');
    Route::get('get-written-off-details', [WrittenOffController::class, 'customer'])->name('get-written-off-details');
    Route::get('truncate-arrears-and-sales', [SaleController::class, 'truncateArrearsAndSales'])->name('truncate-arrears-and-sales');
    Route::get('truncate-written-offs', [WrittenOffController::class, 'truncateWrittenOffs'])->name('truncate-written-offs');
    Route::get('truncate-previous-end-month-sales', [SaleController::class, 'truncatePreviousEndMonth'])->name('truncate-previous-end-month-sales');
    Route::get('download-product-targets-template', [ProductTargetController::class, 'downloadTemplate'])->name('download-product-targets-template');
    Route::get('download-branch-targets-template', [BranchTargetController::class, 'downloadTemplate'])->name('download-branch-targets-template');
    Route::get('download-officer-targets-template', [OfficerTargetController::class, 'downloadTemplate'])->name('download-officer-targets-template');
    Route::get('written-off-customers', [WrittenOffController::class, 'index'])->name('written-off-customers');
    Route::get('/login', function () {
        return view('dashboard');
    })->name('sign-up');
});

Route::get('download-template', [SaleController::class, 'downloadTemplate'])->name('download-template');
Route::get('officers', [OfficerController::class, 'getOfficers'])->name('officers');

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
