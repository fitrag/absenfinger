<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Guru;
use App\Models\Walas;
use App\Models\Pkl;
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

        $userLevel = Session::get('user_level');
        $isStudent = $userLevel === 'siswa';
        $isPkl = false;

        // Get Guru Piket for Today
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $todayDayOfWeek = now()->dayOfWeek; // 0 (Sunday) - 6 (Saturday)
        $hariIndonesia = $days[$todayDayOfWeek];

        $todayGuruPiket = \App\Models\GuruPiket::with('guru')
            ->where('hari', $hariIndonesia)
            ->where('is_active', true)
            ->get();


        // Check if user is Wali Kelas
        $isWaliKelas = in_array('Wali Kelas', $userRoles);
        $walasData = null;
        $kelasInfo = null;

        if ($isStudent) {
            $student = Student::where('user_id', $userId)->first();
            if ($student) {
                $isPkl = Pkl::where('student_id', $student->id)->exists();
            }
        }

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

                    // Get late students (siswa terlambat) for this class - Top 5 Most Frequent
                    $siswaTerlambat = \App\Models\SiswaTerlambat::with('student')
                        ->whereIn('student_id', $studentIds)
                        ->selectRaw('student_id, count(*) as total_terlambat, sum(keterlambatan_menit) as total_menit')
                        ->groupBy('student_id')
                        ->orderByDesc('total_terlambat')
                        ->limit(5)
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

                    // Weekly statistics for this class (excluding weekends)
                    $weeklyData = [];
                    $daysCollected = 0;
                    $daysBack = 0;
                    while ($daysCollected < 7 && $daysBack < 30) {
                        $date = now()->subDays($daysBack);
                        $daysBack++;

                        // Skip Saturday (6) and Sunday (0)
                        if ($date->dayOfWeek == 0 || $date->dayOfWeek == 6) {
                            continue;
                        }

                        $dayName = $date->locale('id')->isoFormat('ddd');
                        $dateStr = $date->toDateString();

                        // Get all records for the day for this class
                        $dailyRecords = Attendance::whereDate('checktime', $dateStr)
                            ->whereIn('nis', $studentNis)
                            ->get()
                            ->groupBy('nis');

                        $hadir = 0;
                        $bolos = 0;
                        $sakit = 0;
                        $izin = 0;
                        $alpha = 0;
                        $recordedNis = [];

                        foreach ($dailyRecords as $nis => $records) {
                            $recordedNis[] = $nis;

                            $special = $records->whereIn('checktype', [2, 3, 4])->first();
                            if ($special) {
                                if ($special->checktype == 2)
                                    $sakit++;
                                elseif ($special->checktype == 3)
                                    $izin++;
                                elseif ($special->checktype == 4)
                                    $alpha++;
                                continue;
                            }

                            $checkIn = $records->where('checktype', 0)->first();
                            $checkOut = $records->where('checktype', 1)->first();

                            if ($checkIn) {
                                if ($checkOut) {
                                    $hadir++;
                                } else {
                                    if ($date->isToday() && now()->format('H:i') < '16:00') {
                                        $hadir++;
                                    } else {
                                        $bolos++;
                                    }
                                }
                            }
                        }

                        $recordedCount = count(array_unique($recordedNis));
                        $unrecorded = max(0, $totalStudents - $recordedCount);
                        $alpha += $unrecorded;

                        $weeklyData[] = [
                            'day' => $dayName,
                            'date' => $date->format('Y-m-d'),
                            'hadir' => $hadir,
                            'bolos' => $bolos,
                            'sakit' => $sakit,
                            'izin' => $izin,
                            'alpha' => $alpha,
                            'present' => $hadir + $bolos,
                            'absent' => $alpha + $sakit + $izin,
                        ];
                        $daysCollected++;
                    }
                    // Reverse to show oldest first
                    $weeklyData = array_reverse($weeklyData);

                    // Weekday statistics (Mon-Fri of current week) for this class with full breakdown
                    $weekdayData = [];
                    $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at'];
                    $startOfWeek = now()->startOfWeek(); // Monday

                    for ($i = 0; $i < 5; $i++) {
                        $date = $startOfWeek->copy()->addDays($i);
                        $dateStr = $date->toDateString();

                        // Get all records for the day for this class
                        $dailyRecords = Attendance::whereDate('checktime', $dateStr)
                            ->whereIn('nis', $studentNis)
                            ->get()
                            ->groupBy('nis');

                        $hadir = 0;
                        $bolos = 0;
                        $sakit = 0;
                        $izin = 0;
                        $alpha = 0;
                        $recordedNis = [];

                        foreach ($dailyRecords as $nis => $records) {
                            $recordedNis[] = $nis;

                            $special = $records->whereIn('checktype', [2, 3, 4])->first();
                            if ($special) {
                                if ($special->checktype == 2)
                                    $sakit++;
                                elseif ($special->checktype == 3)
                                    $izin++;
                                elseif ($special->checktype == 4)
                                    $alpha++;
                                continue;
                            }

                            $checkIn = $records->where('checktype', 0)->first();
                            $checkOut = $records->where('checktype', 1)->first();

                            if ($checkIn) {
                                if ($checkOut) {
                                    $hadir++;
                                } else {
                                    if ($date->isToday() && now()->format('H:i') < '16:00') {
                                        $hadir++;
                                    } else {
                                        $bolos++;
                                    }
                                }
                            }
                        }

                        $recordedCount = count(array_unique($recordedNis));
                        $unrecorded = max(0, $totalStudents - $recordedCount);
                        $alpha += $unrecorded;

                        $weekdayData[] = [
                            'day' => $dayNames[$i],
                            'date' => $date->format('Y-m-d'),
                            'hadir' => $hadir,
                            'bolos' => $bolos,
                            'sakit' => $sakit,
                            'izin' => $izin,
                            'alpha' => $alpha,
                            'present' => $hadir + $bolos,
                            'absent' => $totalStudents - ($hadir + $bolos),
                        ];
                    }

                    // Late student, violation, and counseling trend data for semester genap (Jan-Jun) - aggregated by week
                    $lateTrendData = [];
                    $violationTrendData = [];
                    $counselingTrendData = [];
                    $currentYear = now()->year;
                    $startDate = now()->setDate($currentYear, 1, 1)->startOfWeek(); // 1 Januari
                    $endDate = now()->setDate($currentYear, 6, 30)->endOfWeek(); // 30 Juni

                    $currentWeek = $startDate->copy();
                    while ($currentWeek <= $endDate) {
                        $weekEnd = $currentWeek->copy()->endOfWeek();

                        $lateCount = \App\Models\SiswaTerlambat::whereBetween('tanggal', [$currentWeek->toDateString(), $weekEnd->toDateString()])
                            ->whereIn('student_id', $studentIds)
                            ->count();

                        $violationCount = \App\Models\PdsPelanggaran::whereBetween('tanggal', [$currentWeek->toDateString(), $weekEnd->toDateString()])
                            ->whereIn('student_id', $studentIds)
                            ->count();

                        $counselingCount = \App\Models\PdsKonseling::whereBetween('tanggal', [$currentWeek->toDateString(), $weekEnd->toDateString()])
                            ->whereIn('student_id', $studentIds)
                            ->count();

                        $lateTrendData[] = [
                            'week' => 'Minggu ' . $currentWeek->weekOfYear,
                            'date' => $currentWeek->format('d M'),
                            'month' => $currentWeek->locale('id')->isoFormat('MMM'),
                            'full_date' => $currentWeek->format('Y-m-d'),
                            'count' => $lateCount,
                        ];

                        $violationTrendData[] = [
                            'week' => 'Minggu ' . $currentWeek->weekOfYear,
                            'date' => $currentWeek->format('d M'),
                            'month' => $currentWeek->locale('id')->isoFormat('MMM'),
                            'full_date' => $currentWeek->format('Y-m-d'),
                            'count' => $violationCount,
                        ];

                        $counselingTrendData[] = [
                            'week' => 'Minggu ' . $currentWeek->weekOfYear,
                            'date' => $currentWeek->format('d M'),
                            'month' => $currentWeek->locale('id')->isoFormat('MMM'),
                            'full_date' => $currentWeek->format('Y-m-d'),
                            'count' => $counselingCount,
                        ];

                        $currentWeek->addWeek();
                    }

                    return view('admin.dashboard', compact(
                        'totalStudents',
                        'presentToday',
                        'checkOutToday',
                        'absentToday',
                        'recentAttendances',
                        'weeklyData',
                        'weekdayData',
                        'isWaliKelas',
                        'walasData',
                        'kelasInfo',
                        'siswaTerlambat',
                        'pelanggaranSiswa',
                        'siswaKonseling',
                        'lateTrendData',
                        'violationTrendData',
                        'counselingTrendData',
                        'todayGuruPiket'
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

        // Weekly statistics for chart (excluding Saturday and Sunday)
        $weeklyData = [];
        $daysCollected = 0;
        $daysBack = 0;
        while ($daysCollected < 7 && $daysBack < 30) {
            $date = now()->subDays($daysBack);
            $daysBack++;

            // Skip Saturday (6) and Sunday (0)
            if ($date->dayOfWeek == 0 || $date->dayOfWeek == 6) {
                continue;
            }

            $dayName = $date->locale('id')->isoFormat('ddd');
            $dateStr = $date->toDateString();

            // Get all records for the day
            $dailyRecords = Attendance::whereDate('checktime', $dateStr)->get()->groupBy('nis');

            $hadir = 0;
            $bolos = 0;
            $sakit = 0;
            $izin = 0;
            $alpha = 0;

            // NIS that have some record
            $recordedNis = [];

            foreach ($dailyRecords as $nis => $records) {
                $recordedNis[] = $nis;

                // Priority: S/I/A explicit > Hadir/Bolos
                $special = $records->whereIn('checktype', [2, 3, 4])->first();
                if ($special) {
                    if ($special->checktype == 2)
                        $sakit++;
                    elseif ($special->checktype == 3)
                        $izin++;
                    elseif ($special->checktype == 4)
                        $alpha++;
                    continue;
                }

                // Check presence
                $checkIn = $records->where('checktype', 0)->first();
                $checkOut = $records->where('checktype', 1)->first();

                if ($checkIn) {
                    if ($checkOut) {
                        $hadir++;
                    } else {
                        // Logic Bolos vs Hadir (Belum Pulang)
                        if ($date->isToday() && now()->format('H:i') < '16:00') {
                            $hadir++;
                        } else {
                            $bolos++;
                        }
                    }
                }
            }

            // Calculate Unrecorded Alpha (Total - Recorded)
            $recordedCount = count(array_unique($recordedNis));
            $unrecorded = max(0, $totalStudents - $recordedCount);
            $alpha += $unrecorded;

            $weeklyData[] = [
                'day' => $dayName,
                'date' => $date->format('Y-m-d'),
                'hadir' => $hadir,
                'bolos' => $bolos,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'present' => $hadir + $bolos,
                'absent' => $alpha + $sakit + $izin,
            ];
            $daysCollected++;
        }
        // Reverse to show oldest first
        $weeklyData = array_reverse($weeklyData);

        // Weekday statistics (Mon-Fri of current week)
        $weekdayData = [];
        $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at'];
        $startOfWeek = now()->startOfWeek(); // Monday

        for ($i = 0; $i < 5; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dateStr = $date->toDateString();

            // Get all records for the day
            $dailyRecords = Attendance::whereDate('checktime', $dateStr)->get()->groupBy('nis');

            $hadir = 0;
            $bolos = 0;
            $sakit = 0;
            $izin = 0;
            $alpha = 0;

            // NIS that have some record
            $recordedNis = [];

            foreach ($dailyRecords as $nis => $records) {
                $recordedNis[] = $nis;

                // Priority: S/I/A explicit > Hadir/Bolos
                $special = $records->whereIn('checktype', [2, 3, 4])->first();
                if ($special) {
                    if ($special->checktype == 2)
                        $sakit++;
                    elseif ($special->checktype == 3)
                        $izin++;
                    elseif ($special->checktype == 4)
                        $alpha++;
                    continue;
                }

                // Check presence
                $checkIn = $records->where('checktype', 0)->first();
                $checkOut = $records->where('checktype', 1)->first();

                if ($checkIn) {
                    if ($checkOut) {
                        $hadir++;
                    } else {
                        // If today and not yet 16:00, count as hadir? Or strict bolos?
                        // Usually dashboard shows current state.
                        // If it's today and time < 16:00, usually considered "Hadir (Belum Pulang)".
                        // But user specifically asked for "Bolos".
                        // Let's assume Bolos = CheckIn && !CheckOut implies anomaly for past days.
                        // For TODAY, keep as "Hadir" until EOD?
                        if ($date->isToday() && now()->format('H:i') < '16:00') {
                            $hadir++;
                        } else {
                            $bolos++;
                        }
                    }
                }
            }

            // Calculate Unrecorded Alpha (Total - Recorded)
            // Assuming $totalStudents covers all active students
            $recordedCount = count(array_unique($recordedNis));
            $unrecorded = max(0, $totalStudents - $recordedCount);
            $alpha += $unrecorded;

            $weekdayData[] = [
                'day' => $dayNames[$i],
                'date' => $date->format('Y-m-d'),
                'hadir' => $hadir,
                'bolos' => $bolos,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'present' => $hadir + $bolos, // For backward compatibility if needed
            ];
        }

        // Get late students (siswa terlambat) - Top 5 Most Frequent
        $siswaTerlambat = \App\Models\SiswaTerlambat::with('student.kelas')
            ->selectRaw('student_id, count(*) as total_terlambat, sum(keterlambatan_menit) as total_menit')
            ->groupBy('student_id')
            ->orderByDesc('total_terlambat')
            ->limit(5)
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

        // Late student, violation, and counseling trend data for semester genap (Jan-Jun) - aggregated by week
        $lateTrendData = [];
        $violationTrendData = [];
        $counselingTrendData = [];
        $currentYear = now()->year;
        $startDate = now()->setDate($currentYear, 1, 1)->startOfWeek(); // 1 Januari
        $endDate = now()->setDate($currentYear, 6, 30)->endOfWeek(); // 30 Juni

        $currentWeek = $startDate->copy();
        while ($currentWeek <= $endDate) {
            $weekEnd = $currentWeek->copy()->endOfWeek();

            $lateCount = \App\Models\SiswaTerlambat::whereBetween('tanggal', [$currentWeek->toDateString(), $weekEnd->toDateString()])
                ->count();

            $violationCount = \App\Models\PdsPelanggaran::whereBetween('tanggal', [$currentWeek->toDateString(), $weekEnd->toDateString()])
                ->count();

            $counselingCount = \App\Models\PdsKonseling::whereBetween('tanggal', [$currentWeek->toDateString(), $weekEnd->toDateString()])
                ->count();

            $lateTrendData[] = [
                'week' => 'Minggu ' . $currentWeek->weekOfYear,
                'date' => $currentWeek->format('d M'),
                'month' => $currentWeek->locale('id')->isoFormat('MMM'),
                'full_date' => $currentWeek->format('Y-m-d'),
                'count' => $lateCount,
            ];

            $violationTrendData[] = [
                'week' => 'Minggu ' . $currentWeek->weekOfYear,
                'date' => $currentWeek->format('d M'),
                'month' => $currentWeek->locale('id')->isoFormat('MMM'),
                'full_date' => $currentWeek->format('Y-m-d'),
                'count' => $violationCount,
            ];

            $counselingTrendData[] = [
                'week' => 'Minggu ' . $currentWeek->weekOfYear,
                'date' => $currentWeek->format('d M'),
                'month' => $currentWeek->locale('id')->isoFormat('MMM'),
                'full_date' => $currentWeek->format('Y-m-d'),
                'count' => $counselingCount,
            ];

            $currentWeek->addWeek();
        }

        // Get Guru Piket for Today
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $todayDayOfWeek = now()->dayOfWeek; // 0 (Sunday) - 6 (Saturday)
        $hariIndonesia = $days[$todayDayOfWeek];

        $todayGuruPiket = \App\Models\GuruPiket::with('guru')
            ->where('hari', $hariIndonesia)
            ->where('is_active', true)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'presentToday',
            'checkOutToday',
            'absentToday',
            'recentAttendances',
            'weeklyData',
            'weekdayData',
            'isWaliKelas',
            'walasData',
            'kelasInfo',
            'siswaTerlambat',
            'pelanggaranSiswa',
            'siswaKonseling',
            'lateTrendData',
            'violationTrendData',
            'counselingTrendData',
            'isStudent',
            'isPkl',
            'todayGuruPiket'
        ));
    }
}
