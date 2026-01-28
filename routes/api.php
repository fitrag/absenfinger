<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\PklAttendanceController;

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

    // Get students with absence status (sakit/izin)
    Route::get('/absences', [AttendanceController::class, 'absences']);
});

/*
|--------------------------------------------------------------------------
| PKL Attendance API Routes
|--------------------------------------------------------------------------
|
| Endpoint untuk absensi siswa PKL (Praktik Kerja Lapangan).
| Menggunakan validasi lokasi GPS.
|
*/

Route::prefix('pkl')->group(function () {
    // Check-in for PKL student
    Route::post('/check-in', [PklAttendanceController::class, 'checkIn']);

    // Check-out for PKL student
    Route::post('/check-out', [PklAttendanceController::class, 'checkOut']);

    // Get today's attendance status
    Route::get('/status', [PklAttendanceController::class, 'status']);

    // Get attendance history
    Route::get('/history', [PklAttendanceController::class, 'history']);
});
