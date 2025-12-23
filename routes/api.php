<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Attendance API Routes
|--------------------------------------------------------------------------
|
| Endpoint untuk menerima data absensi dari server lokal fingerprint.
| Tidak menggunakan auth karena diakses dari server lokal.
|
*/

Route::prefix('attendance')->group(function () {
    // Sync attendance data from fingerprint machine
    Route::post('/sync', [AttendanceController::class, 'sync']);
    
    // Get attendance records
    Route::get('/', [AttendanceController::class, 'index']);
    
    // Get today's attendance summary
    Route::get('/today', [AttendanceController::class, 'today']);
});
