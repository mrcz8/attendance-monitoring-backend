<?php

use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LicenseKeyController;
use App\Http\Controllers\ShiftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->group(function() {
        Route::prefix('users')->group(function (    ) {
            Route::get('me', function (Request $request) {
                return $request->user();
            });
        });

        Route::prefix('client')->group(function () {
            Route::get('/', [ClientController::class, 'list']);
            Route::post('/', [ClientController::class, 'store']);
            Route::put('/{id}', [ClientController::class, 'update']);
            Route::delete('/deactivate/{id}', [ClientController::class, 'deactivate']);
            Route::delete('/restore/{id}', [ClientController::class, 'restore']);
            Route::delete('/delete/{id}', [ClientController::class, 'delete']);
            Route::post('/onboard', [ClientController::class, 'onboard']);
        });

        Route::prefix('license')->group(function () {
            Route::get('/', [LicenseKeyController::class, 'list']);
            Route::post('/', [LicenseKeyController::class, 'generate']);
            Route::delete('/{id}', [LicenseKeyController::class, 'delete']);
        });

        Route::prefix('department')->group(function () {
            Route::get('/', [DepartmentController::class, 'list']);
            Route::get('/{id}', [DepartmentController::class, 'find']);
            Route::put('/{id}', [DepartmentController::class, 'update']);
            Route::delete('/{id}', [DepartmentController::class, 'delete']);
            Route::post('/', [DepartmentController::class, 'store']);
        });

        Route::prefix('shift')->group(function () {
            Route::get('/', [ShiftController::class, 'list']);
            Route::get('/{id}', [ShiftController::class, 'find']);
            Route::post('/', [ShiftController::class, 'store']);
            Route::put('/{id}', [ShiftController::class, 'update']);
            Route::delete('/{id}', [ShiftController::class, 'delete']);
        });

        Route::prefix('employee')->group(function () {
            Route::get('/', [EmployeeController::class, 'list']);
            Route::post('/', [EmployeeController::class, 'store']);
            Route::get('/getDeptShift', [EmployeeController::class, 'getDeptShift']);
            Route::get('/{id}', [EmployeeController::class, 'find']);
            Route::put('/{id}', [EmployeeController::class, 'update']);
            Route::delete('/{id}', [EmployeeController::class, 'delete']);
        });

        Route::prefix('attendance')->group(function () {
            Route::get('/logs', [AttendanceRecordController::class, 'index']);
        });

        Route::prefix('import')->group(function () {
            Route::post('/logs', [AttendanceRecordController::class, 'importAttendanceLogs']);
            Route::post('/shift', [AttendanceRecordController::class, 'importShiftSettings']);
        });
    });
});