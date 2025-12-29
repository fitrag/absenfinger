<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Guru;
use App\Models\Walas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $today = now()->toDateString();
        $userId = Session::get('user_id');
        $userRoles = session('user_roles', []);

        // Check if user is Wali Kelas
        $isWaliKelas = in_array('Wali Kelas', $userRoles);
        $walasData = null;
        $kelasInfo = null;

        if ($isWaliKelas && $userId) {
            // Get guru data based on user_id
            $guru = Guru::where('user_id', $userId)->first();

            if ($guru) {
                // Get walas data with kelas relation
                $walasData = Walas::with('kelas')
                    ->where('guru_id', $guru->id)
                    ->where('is_active', true)
                    ->first();

                if ($walasData && $walasData->kelas) {
                    $kelasId = $walasData->kelas_id;
                    $kelasInfo = $walasData->kelas;

                    // Get students in this class
                    $totalStudents = Student::where('kelas_id', $kelasId)
                        ->where('is_active', true)
                        ->count();

                    // Get NIS of students in this class
                    $studentNis = Student::where('kelas_id', $kelasId)
                        ->where('is_active', true)
                        ->pluck('nis')
                        ->toArray();

                    // Get student IDs in this class
                    $studentIds = Student::where('kelas_id', $kelasId)
                        ->where('is_active', true)
                        ->pluck('id')
                        ->toArray();

                    // Present today in this class
                    $presentToday = Attendance::whereDate('checktime', $today)
                        ->where('checktype', 0)
                        ->whereIn('nis', $studentNis)
                        ->distinct('nis')
                        ->count('nis');

                    // Checkout today in this class
                    $checkOutToday = Attendance::whereDate('checktime', $today)
                        ->where('checktype', 1)
                        ->whereIn('nis', $studentNis)
                        ->distinct('nis')
                        ->count('nis');

                    $absentToday = $totalStudents - $presentToday;

                    // Recent attendances for this class
                    $recentAttendances = Attendance::with('student')
                        ->whereDate('checktime', $today)
                        ->whereIn('nis', $studentNis)
                        ->orderBy('checktime', 'desc')
                        ->limit(10)
                        ->get();

                    // Get late students (siswa terlambat) for this class - recent 10
                    $siswaTerlambat = \App\Models\SiswaTerlambat::with('student')
                        ->whereIn('student_id', $studentIds)
                        ->orderBy('tanggal', 'desc')
                        ->orderBy('jam_datang', 'desc')
                        ->limit(10)
                        ->get();

                    // Get students with violations and their total points for this class
                    $pelanggaranSiswa = \App\Models\PdsPelanggaran::with('student')
                        ->whereIn('student_id', $studentIds)
                        ->selectRaw('student_id, SUM(poin) as total_poin, COUNT(*) as jumlah_pelanggaran')
                        ->groupBy('student_id')
                        ->orderByDesc('total_poin')
                        ->limit(10)
                        ->get()
                        ->map(function ($item) {
                            $student = \App\Models\Student::with('kelas')->find($item->student_id);

                            // Get violation types
                            $types = \App\Models\PdsPelanggaran::where('student_id', $item->student_id)
                                ->distinct()
                                ->pluck('jenis_pelanggaran')
                                ->toArray();

                            return [
                                'student' => $student,
                                'total_poin' => $item->total_poin,
                                'jumlah_pelanggaran' => $item->jumlah_pelanggaran,
                                'jenis_pelanggaran' => $types,
                            ];
                        });

                    // Get counseling data for this class - recent 5
                    $siswaKonseling = \App\Models\PdsKonseling::with('student')
                        ->whereIn('student_id', $studentIds)
                        ->orderBy('tanggal', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();

                    // Weekly statistics for this class
                    $weeklyData = [];
                    for ($i = 6; $i >= 0; $i--) {
                        $date = now()->subDays($i);
                        $dayName = $date->locale('id')->isoFormat('ddd');

                        $present = Attendance::whereDate('checktime', $date->toDateString())
                            ->where('checktype', 0)
                            ->whereIn('nis', $studentNis)
                            ->distinct('nis')
                            ->count('nis');

                        $weeklyData[] = [
                            'day' => $dayName,
                            'date' => $date->format('Y-m-d'),
                            'present' => $present,
                            'absent' => $totalStudents - $present,
                        ];
                    }

                    return view('admin.dashboard', compact(
                        'totalStudents',
                        'presentToday',
                        'checkOutToday',
                        'absentToday',
                        'recentAttendances',
                        'weeklyData',
                        'isWaliKelas',
                        'walasData',
                        'kelasInfo',
                        'siswaTerlambat',
                        'pelanggaranSiswa',
                        'siswaKonseling'
                    ));
                }
            }
        }

        // Default: Admin dashboard (all students)
        $totalStudents = Student::active()->count();
        $presentToday = Attendance::whereDate('checktime', $today)
            ->where('checktype', 0)
            ->distinct('nis')
            ->count('nis');
        $checkOutToday = Attendance::whereDate('checktime', $today)
            ->where('checktype', 1)
            ->distinct('nis')
            ->count('nis');
        $absentToday = $totalStudents - $presentToday;

        // Recent attendances
        $recentAttendances = Attendance::with('student')
            ->whereDate('checktime', $today)
            ->orderBy('checktime', 'desc')
            ->limit(10)
            ->get();

        // Weekly statistics for chart
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->locale('id')->isoFormat('ddd');

            $present = Attendance::whereDate('checktime', $date->toDateString())
                ->where('checktype', 0)
                ->distinct('nis')
                ->count('nis');

            $weeklyData[] = [
                'day' => $dayName,
                'date' => $date->format('Y-m-d'),
                'present' => $present,
                'absent' => $totalStudents - $present,
            ];
        }

        // Get late students (siswa terlambat) - recent 10
        $siswaTerlambat = \App\Models\SiswaTerlambat::with('student.kelas')
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_datang', 'desc')
            ->limit(10)
            ->get();

        // Get students with violations and their total points (Top 10)
        $pelanggaranSiswa = \App\Models\PdsPelanggaran::with('student')
            ->selectRaw('student_id, SUM(poin) as total_poin, COUNT(*) as jumlah_pelanggaran')
            ->groupBy('student_id')
            ->orderByDesc('total_poin')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $student = \App\Models\Student::with('kelas')->find($item->student_id);

                // Get violation types
                $types = \App\Models\PdsPelanggaran::where('student_id', $item->student_id)
                    ->distinct()
                    ->pluck('jenis_pelanggaran')
                    ->toArray();

                return [
                    'student' => $student,
                    'total_poin' => $item->total_poin,
                    'jumlah_pelanggaran' => $item->jumlah_pelanggaran,
                    'jenis_pelanggaran' => $types,
                ];
            });

        // Get counseling data - recent 10
        $siswaKonseling = \App\Models\PdsKonseling::with('student.kelas')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'presentToday',
            'checkOutToday',
            'absentToday',
            'recentAttendances',
            'weeklyData',
            'isWaliKelas',
            'walasData',
            'kelasInfo',
            'siswaTerlambat',
            'pelanggaranSiswa',
            'siswaKonseling'
        ));
    }
}
