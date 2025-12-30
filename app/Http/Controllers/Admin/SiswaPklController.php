<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pkl;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class SiswaPklController extends Controller
{
    /**
     * Get the logged-in student's PKL data.
     */
    private function getStudentPkl()
    {
        $userId = Session::get('user_id');

        // Find student by user_id
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return null;
        }

        // Get active PKL for this student
        $pkl = Pkl::with(['dudi', 'pembimbingSekolah'])
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        return [
            'student' => $student,
            'pkl' => $pkl,
        ];
    }

    /**
     * Display PKL dashboard.
     */
    public function dashboard()
    {
        $data = $this->getStudentPkl();

        if (!$data || !$data['pkl']) {
            return view('admin.siswa.pkl.no-pkl');
        }

        $student = $data['student'];
        $pkl = $data['pkl'];
        $dudi = $pkl->dudi;

        // Get today's PKL attendance
        $todayCheckIn = Attendance::where('nis', $student->nis)
            ->where('dudi_id', $dudi->id)
            ->pkl()
            ->whereDate('checktime', today())
            ->checkIn()
            ->first();

        $todayCheckOut = Attendance::where('nis', $student->nis)
            ->where('dudi_id', $dudi->id)
            ->pkl()
            ->whereDate('checktime', today())
            ->checkOut()
            ->first();

        // Get this month's PKL attendance summary
        $monthlyAttendance = Attendance::where('nis', $student->nis)
            ->where('dudi_id', $dudi->id)
            ->pkl()
            ->whereMonth('checktime', now()->month)
            ->whereYear('checktime', now()->year)
            ->checkIn()
            ->count();

        return view('admin.siswa.pkl.dashboard', compact(
            'student',
            'pkl',
            'dudi',
            'todayCheckIn',
            'todayCheckOut',
            'monthlyAttendance'
        ));
    }

    /**
     * Store check-in attendance.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $data = $this->getStudentPkl();

        if (!$data || !$data['pkl']) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar PKL'
            ], 400);
        }

        $student = $data['student'];
        $dudi = $data['pkl']->dudi;

        // Check if already checked in today
        $existingCheckIn = Attendance::where('nis', $student->nis)
            ->where('dudi_id', $dudi->id)
            ->pkl()
            ->whereDate('checktime', today())
            ->checkIn()
            ->first();

        if ($existingCheckIn) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen masuk hari ini'
            ], 400);
        }

        // Validate location
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $dudi->latitude,
            $dudi->longitude
        );

        if ($distance > $dudi->radius) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar area DUDI. Jarak Anda: ' . round($distance) . ' meter (maksimal: ' . $dudi->radius . ' meter)'
            ], 400);
        }

        // Store attendance
        Attendance::create([
            'nis' => $student->nis,
            'dudi_id' => $dudi->id,
            'checktime' => now(),
            'checktype' => Attendance::TYPE_MASUK,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_pkl' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen masuk berhasil! Jarak: ' . round($distance) . ' meter'
        ]);
    }

    /**
     * Store check-out attendance.
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $data = $this->getStudentPkl();

        if (!$data || !$data['pkl']) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar PKL'
            ], 400);
        }

        $student = $data['student'];
        $dudi = $data['pkl']->dudi;

        // Check if checked in today
        $existingCheckIn = Attendance::where('nis', $student->nis)
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
        $existingCheckOut = Attendance::where('nis', $student->nis)
            ->where('dudi_id', $dudi->id)
            ->pkl()
            ->whereDate('checktime', today())
            ->checkOut()
            ->first();

        if ($existingCheckOut) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen pulang hari ini'
            ], 400);
        }

        // Validate location
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $dudi->latitude,
            $dudi->longitude
        );

        if ($distance > $dudi->radius) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar area DUDI. Jarak Anda: ' . round($distance) . ' meter (maksimal: ' . $dudi->radius . ' meter)'
            ], 400);
        }

        // Store attendance
        Attendance::create([
            'nis' => $student->nis,
            'dudi_id' => $dudi->id,
            'checktime' => now(),
            'checktype' => Attendance::TYPE_PULANG,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_pkl' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen pulang berhasil!'
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
