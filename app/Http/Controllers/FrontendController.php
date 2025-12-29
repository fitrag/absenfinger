<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Jurusan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    /**
     * Display the home page.
     */
    public function home()
    {
        // Get counts
        $totalStudents = Student::count();
        $totalGurus = Guru::count();
        $totalKelas = Kelas::count();
        $totalJurusan = Jurusan::count();

        // Get today's attendance stats
        $today = Carbon::today();
        $attendanceToday = Attendance::whereDate('checktime', $today)
            ->get()
            ->groupBy('nis');

        $hadir = 0;
        foreach ($attendanceToday as $nis => $records) {
            $checkIn = $records->where('checktype', 0)->first();
            $checkOut = $records->where('checktype', 1)->first();
            if ($checkIn && $checkOut) {
                $hadir++;
            }
        }

        $tidakHadir = $totalStudents - $hadir;
        $persentase = $totalStudents > 0 ? round(($hadir / $totalStudents) * 100, 1) : 0;

        $stats = [
            'total_students' => $totalStudents,
            'total_gurus' => $totalGurus,
            'total_kelas' => $totalKelas,
            'total_jurusan' => $totalJurusan,
            'hadir' => $hadir,
            'tidak_hadir' => $tidakHadir,
            'persentase' => $persentase,
        ];

        return view('frontend.home', compact('stats'));
    }

    /**
     * Display the presensi page with daily attendance.
     */
    public function presensi(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $kelasId = $request->get('kelas_id');

        // Get today's date info
        $today = Carbon::parse($date);

        // Get all kelas for filter
        $kelasList = Kelas::orderBy('nm_kls')->get();

        // Build student query
        $studentQuery = Student::with('kelas');
        if ($kelasId) {
            $studentQuery->where('kelas_id', $kelasId);
        }
        $students = $studentQuery->orderBy('name')->get();

        // Get attendance data for selected date
        $attendanceData = Attendance::whereDate('checktime', $date)
            ->get()
            ->groupBy('nis');

        // Process attendance for each student
        $attendanceList = [];
        $stats = [
            'hadir' => 0,
            'terlambat' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
            'bolos' => 0,
            'belum' => 0,
        ];

        foreach ($students as $student) {
            $records = $attendanceData->get($student->nis);

            $status = 'Belum Absen';
            $statusClass = 'slate';
            $checkIn = null;
            $checkOut = null;

            if ($records) {
                $checkInRecord = $records->where('checktype', 0)->first();
                $checkOutRecord = $records->where('checktype', 1)->first();
                $specialRecord = $records->whereIn('checktype', [2, 3, 4])->first();

                if ($specialRecord) {
                    if ($specialRecord->checktype == 2) {
                        $status = 'Sakit';
                        $statusClass = 'amber';
                        $stats['sakit']++;
                    } elseif ($specialRecord->checktype == 3) {
                        $status = 'Izin';
                        $statusClass = 'blue';
                        $stats['izin']++;
                    } elseif ($specialRecord->checktype == 4) {
                        $status = 'Alpha';
                        $statusClass = 'rose';
                        $stats['alpha']++;
                    }
                } elseif ($checkInRecord) {
                    $checkIn = Carbon::parse($checkInRecord->checktime)->format('H:i');

                    if ($checkOutRecord) {
                        $checkOut = Carbon::parse($checkOutRecord->checktime)->format('H:i');

                        // Check if late (after 07:30)
                        $checkInTime = Carbon::parse($checkInRecord->checktime);
                        if ($checkInTime->format('H:i') > '07:30') {
                            $status = 'Terlambat';
                            $statusClass = 'amber';
                            $stats['terlambat']++;
                        } else {
                            $status = 'Hadir';
                            $statusClass = 'emerald';
                            $stats['hadir']++;
                        }
                    } else {
                        $status = 'Bolos';
                        $statusClass = 'rose';
                        $stats['bolos']++;
                    }
                } else {
                    $stats['belum']++;
                }
            } else {
                $stats['belum']++;
            }

            $attendanceList[] = [
                'student' => $student,
                'status' => $status,
                'statusClass' => $statusClass,
                'checkIn' => $checkIn,
                'checkOut' => $checkOut,
            ];
        }

        // Generate monthly chart data (current year)
        $currentYear = Carbon::now()->year;
        $monthlyData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            // Get all attendance for this month
            $monthAttendance = Attendance::whereBetween('checktime', [$startDate, $endDate])
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->checktime)->format('Y-m-d') . '-' . $item->nis;
                });

            $tepatWaktu = 0;
            $terlambat = 0;
            $pulangAwal = 0;
            $tidakAbsen = 0;

            // Group by day-nis unique combinations
            $dailyRecords = Attendance::whereBetween('checktime', [$startDate, $endDate])
                ->get()
                ->groupBy('nis');

            foreach ($dailyRecords as $nis => $records) {
                // Group by date
                $byDate = $records->groupBy(function ($r) {
                    return Carbon::parse($r->checktime)->format('Y-m-d');
                });

                foreach ($byDate as $dateStr => $dayRecords) {
                    $checkIn = $dayRecords->where('checktype', 0)->first();
                    $checkOut = $dayRecords->where('checktype', 1)->first();
                    $specialRecord = $dayRecords->whereIn('checktype', [2, 3, 4])->first();

                    if ($specialRecord) {
                        $tidakAbsen++;
                    } elseif ($checkIn && $checkOut) {
                        $checkInTime = Carbon::parse($checkIn->checktime)->format('H:i');
                        $checkOutTime = Carbon::parse($checkOut->checktime)->format('H:i');

                        if ($checkInTime > '07:30') {
                            $terlambat++;
                        } else {
                            $tepatWaktu++;
                        }

                        if ($checkOutTime < '15:00') {
                            $pulangAwal++;
                        }
                    } elseif ($checkIn && !$checkOut) {
                        $pulangAwal++;
                    } else {
                        $tidakAbsen++;
                    }
                }
            }

            $monthlyData[] = [
                'tepat_waktu' => $tepatWaktu,
                'terlambat' => $terlambat,
                'pulang_awal' => $pulangAwal,
                'tidak_absen' => $tidakAbsen,
            ];
        }

        // Calculate yearly totals for pie chart
        $yearlyData = [
            'tepat_waktu' => array_sum(array_column($monthlyData, 'tepat_waktu')),
            'terlambat' => array_sum(array_column($monthlyData, 'terlambat')),
            'pulang_awal' => array_sum(array_column($monthlyData, 'pulang_awal')),
            'tidak_absen' => array_sum(array_column($monthlyData, 'tidak_absen')),
        ];

        return view('frontend.presensi', compact(
            'attendanceList',
            'stats',
            'kelasList',
            'date',
            'today',
            'kelasId',
            'monthlyData',
            'yearlyData',
            'months'
        ));
    }

    /**
     * Display the kesiswaan page.
     */
    public function kesiswaan(Request $request)
    {
        $kelasId = $request->get('kelas_id');

        // Get all kelas for filter
        $kelasList = Kelas::orderBy('nm_kls')->get();

        // Build pelanggaran query
        $pelanggaranQuery = \App\Models\PdsPelanggaran::with(['student.kelas'])
            ->orderBy('tanggal', 'desc');

        if ($kelasId) {
            $pelanggaranQuery->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $pelanggarans = $pelanggaranQuery->limit(10)->get();

        // Build konseling query
        $konselingQuery = \App\Models\PdsKonseling::with(['student.kelas'])
            ->orderBy('tanggal', 'desc');

        if ($kelasId) {
            $konselingQuery->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $konselings = $konselingQuery->limit(10)->get();

        // Statistics
        $stats = [
            'total_pelanggaran' => \App\Models\PdsPelanggaran::count(),
            'total_konseling' => \App\Models\PdsKonseling::count(),
            'pelanggaran_pending' => \App\Models\PdsPelanggaran::where('status', 'pending')->count(),
            'konseling_pending' => \App\Models\PdsKonseling::where('status', 'pending')->count(),
        ];

        return view('frontend.kesiswaan', compact(
            'pelanggarans',
            'konselings',
            'kelasList',
            'kelasId',
            'stats'
        ));
    }
}
