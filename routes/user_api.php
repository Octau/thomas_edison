<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ReportController;
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

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
})->name('health');

Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');

Route::middleware(['auth:user_api', 'activity_logger'])->group(function () {
    Route::post('auth/revoke', [AuthController::class, 'revoke'])->name('auth.revoke');

    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('inventories', InventoryController::class);
    Route::apiResource('purchases', PurchaseController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('transactions', TransactionController::class, ['except' => ['update']]);
    Route::apiResource('users', UserController::class);

    Route::get('get-activities/{subject_id}', [ActivityController::class, 'getActivity']);

    Route::prefix('reports')->group(static function() {
        Route::get('get-transactions', [ReportController::class, 'getTransaction'])->name('reports.get-transactions');
        Route::post('get-transactions/export', [ReportController::class, 'getTransactionExport'])->name('reports.get-transactions-export');
        Route::get('get-purchases', [ReportController::class, 'getPurchase'])->name('reports.get-purchases');
        Route::post('get-purchases/export', [ReportController::class, 'getPurchaseExport'])->name('reports.get-purchases-export');
    });

    Route::prefix('me')->group(static function () {
        Route::get('', [AuthController::class, 'getMe'])->name('me');
        Route::post('auth/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    });

});
