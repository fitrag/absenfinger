<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Pkl;
use App\Models\Dudi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PklAttendanceController extends Controller
{
    /**
     * Check-in for PKL student.
     *
     * POST /api/pkl/check-in
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkIn(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|exists:students,nis',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get student and PKL data
        $student = Student::where('nis', $request->nis)->first();
        $pkl = Pkl::with('dudi')
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        if (!$pkl) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa belum terdaftar PKL'
            ], 400);
        }

        $dudi = $pkl->dudi;

        if (!$dudi) {
            return response()->json([
                'success' => false,
                'message' => 'Data DUDI tidak ditemukan'
            ], 400);
        }

        // Check if already checked in today
        $existingCheckIn = Attendance::where('nis', $request->nis)
            ->where('dudi_id', $dudi->id)
            ->pkl()
            ->whereDate('checktime', today())
            ->checkIn()
            ->first();

        if ($existingCheckIn) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen masuk hari ini',
                'data' => [
                    'checktime' => $existingCheckIn->checktime->format('Y-m-d H:i:s')
                ]
            ], 400);
        }

        // Validate location
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $dudi->latitude,
            $dudi->longitude
        );

        if ($dudi->latitude && $dudi->longitude && $distance > ($dudi->radius ?? 100)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar area DUDI',
                'data' => [
                    'distance' => round($distance),
                    'max_distance' => $dudi->radius ?? 100
                ]
            ], 400);
        }

        // Store attendance
        $attendance = Attendance::create([
            'nis' => $request->nis,
            'dudi_id' => $dudi->id,
            'checktime' => now(),
            'checktype' => Attendance::TYPE_MASUK,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_pkl' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen masuk berhasil',
            'data' => [
                'checktime' => $attendance->checktime->format('Y-m-d H:i:s'),
                'distance' => round($distance),
                'dudi' => $dudi->nama
            ]
        ]);
    }

    /**
     * Check-out for PKL student.
     *
     * POST /api/pkl/check-out
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkOut(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|exists:students,nis',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get student and PKL data
        $student = Student::where('nis', $request->nis)->first();
        $pkl = Pkl::with('dudi')
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        if (!$pkl) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa belum terdaftar PKL'
            ], 400);
        }

        $dudi = $pkl->dudi;

        if (!$dudi) {
            return response()->json([
                'success' => false,
                'message' => 'Data DUDI tidak ditemukan'
            ], 400);
        }

        // Check if checked in today
        $existingCheckIn = Attendance::where('nis', $request->nis)
            ->where('dudi_id', $dudi->id)
            ->pkl()
            ->whereDate('checktime', today())
            ->checkIn()
            ->first();

        if (!$existingCheckIn) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum absen masuk hari ini'
            ], 400);
        }

        // Check if already checked out
        $existingCheckOut = Attendance::where('nis', $request->nis)
            ->where('dudi_id', $dudi->id)
            ->pkl()
            ->whereDate('checktime', today())
            ->checkOut()
            ->first();

        if ($existingCheckOut) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen pulang hari ini',
                'data' => [
                    'checktime' => $existingCheckOut->checktime->format('Y-m-d H:i:s')
                ]
            ], 400);
        }

        // Validate location
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $dudi->latitude,
            $dudi->longitude
        );

        if ($dudi->latitude && $dudi->longitude && $distance > ($dudi->radius ?? 100)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar area DUDI',
                'data' => [
                    'distance' => round($distance),
                    'max_distance' => $dudi->radius ?? 100
                ]
            ], 400);
        }

        // Store attendance
        $attendance = Attendance::create([
            'nis' => $request->nis,
            'dudi_id' => $dudi->id,
            'checktime' => now(),
            'checktype' => Attendance::TYPE_PULANG,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_pkl' => true,
        ]);

        // Calculate work duration
        $duration = $existingCheckIn->checktime->diffInMinutes($attendance->checktime);
        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        return response()->json([
            'success' => true,
            'message' => 'Absen pulang berhasil',
            'data' => [
                'checktime' => $attendance->checktime->format('Y-m-d H:i:s'),
                'distance' => round($distance),
                'duration' => "{$hours} jam {$minutes} menit",
                'dudi' => $dudi->nama
            ]
        ]);
    }

    /**
     * Get today's attendance status.
     *
     * GET /api/pkl/status?nis=2024001
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function status(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|exists:students,nis',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get student and PKL data
        $student = Student::where('nis', $request->nis)->first();
        $pkl = Pkl::with('dudi')
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        if (!$pkl) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa belum terdaftar PKL'
            ], 400);
        }

        $dudi = $pkl->dudi;

        // Get today's attendance
        $checkIn = Attendance::where('nis', $request->nis)
            ->where('dudi_id', $dudi->id)
            ->pkl()
            ->whereDate('checktime', today())
            ->checkIn()
            ->first();

        $checkOut = Attendance::where('nis', $request->nis)
            ->where('dudi_id', $dudi->id)
            ->pkl()
            ->whereDate('checktime', today())
            ->checkOut()
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'nis' => $student->nis,
                    'name' => $student->name,
                ],
                'dudi' => [
                    'id' => $dudi->id,
                    'nama' => $dudi->nama,
                    'alamat' => $dudi->alamat,
                ],
                'today' => [
                    'date' => today()->format('Y-m-d'),
                    'check_in' => $checkIn ? $checkIn->checktime->format('H:i:s') : null,
                    'check_out' => $checkOut ? $checkOut->checktime->format('H:i:s') : null,
                    'is_complete' => $checkIn && $checkOut,
                ]
            ]
        ]);
    }

    /**
     * Get attendance history.
     *
     * GET /api/pkl/history?nis=2024001&month=2026-01
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function history(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|exists:students,nis',
            'month' => 'nullable|date_format:Y-m',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $month = $request->month ? Carbon::parse($request->month . '-01') : now();

        // Get student and PKL data
        $student = Student::where('nis', $request->nis)->first();
        $pkl = Pkl::with('dudi')
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        if (!$pkl) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa belum terdaftar PKL'
            ], 400);
        }

        $dudi = $pkl->dudi;

        // Get monthly attendance
        $attendances = Attendance::where('nis', $request->nis)
            ->where('dudi_id', $dudi->id)
            ->pkl()
            ->whereMonth('checktime', $month->month)
            ->whereYear('checktime', $month->year)
            ->orderBy('checktime')
            ->get()
            ->groupBy(function ($item) {
                return $item->checktime->format('Y-m-d');
            });

        $history = [];
        foreach ($attendances as $date => $records) {
            $checkIn = $records->where('checktype', Attendance::TYPE_MASUK)->first();
            $checkOut = $records->where('checktype', Attendance::TYPE_PULANG)->first();

            $history[] = [
                'date' => $date,
                'day' => Carbon::parse($date)->isoFormat('dddd'),
                'check_in' => $checkIn ? $checkIn->checktime->format('H:i:s') : null,
                'check_out' => $checkOut ? $checkOut->checktime->format('H:i:s') : null,
                'is_complete' => $checkIn && $checkOut,
            ];
        }

        // Summary
        $totalDays = count($history);
        $completeDays = collect($history)->where('is_complete', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'nis' => $student->nis,
                    'name' => $student->name,
                ],
                'dudi' => [
                    'nama' => $dudi->nama,
                ],
                'month' => $month->format('Y-m'),
                'summary' => [
                    'total_days' => $totalDays,
                    'complete_days' => $completeDays,
                ],
                'history' => $history
            ]
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     * Returns distance in meters.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        if (!$lat2 || !$lon2) {
            return 0; // If DUDI has no coordinates, allow attendance
        }

        $earthRadius = 6371000; // Earth's radius in meters

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
