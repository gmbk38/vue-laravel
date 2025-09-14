<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\PayerController;
use App\Http\Controllers\AlfaBankController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::post('...', [PayerController::class, 'create']);
Route::post('...', [PayerController::class, 'update']);
Route::post('...', [PayerController::class, 'delete']);
Route::get('...', [PayerController::class, 'load']);

Route::get('...', [InvoiceController::class, 'loadDevList']);

Route::post('...', [InvoiceController::class, 'create']);
Route::post('...', [InvoiceController::class, 'delete']);
Route::get('...', [InvoiceController::class, 'load']);
Route::get('...', [InvoiceController::class, 'genFile']);

Route::post('...', [MasterController::class, 'updateMasterDevice']);
Route::post('...', [MasterController::class, 'updateMasterDevicesGroup']);
Route::get('...', [MasterController::class, 'getMasterDevices']);

Route::post('...', [MasterController::class, 'createMasterAdmin']);
Route::post('...', [MasterController::class, 'createMasterSession']);

Route::get('...', [AlfaBankController::class, 'test']);
Route::post('...', [AlfaBankController::class, 'updateCode']);
Route::post('...', [AlfaBankController::class, 'manualTokenRefresh']);

Route::get('...', [DashboardController::class, 'test']);