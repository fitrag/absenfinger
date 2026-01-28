<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Jurusan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $totalSiswa = $students->count();
        $stats = [
            'total_siswa' => $totalSiswa,
            'masuk' => 0,
            'pulang' => 0,
            'hadir' => 0,
            'terlambat' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
            'bolos' => 0,
            'belum' => 0,
            'persentase' => 0,
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
                    // Count as checked in (masuk)
                    $stats['masuk']++;

                    $checkIn = Carbon::parse($checkInRecord->checktime)->format('H:i');

                    if ($checkOutRecord) {
                        // Count as checked out (pulang)
                        $stats['pulang']++;

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
                        // Only count as bolos if current time is past 16:00
                        $currentTime = Carbon::now()->format('H:i');
                        if ($currentTime > '16:00') {
                            $status = 'Bolos';
                            $statusClass = 'rose';
                            $stats['bolos']++;
                        }
                        // If before 16:00, don't count as bolos yet (student might still be in school)
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

        // Calculate attendance percentage (hadir + terlambat = total yang masuk dan pulang tepat waktu/terlambat)
        if ($totalSiswa > 0) {
            $stats['persentase'] = round((($stats['hadir'] + $stats['terlambat']) / $totalSiswa) * 100, 1);
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
        $tpId = $request->get('tp_id');
        $tanggalTerlambat = $request->get('tanggal_terlambat');

        // Get all kelas for filter
        $kelasList = Kelas::orderBy('nm_kls')->get();

        // Get all tahun pelajaran for filter
        $tpList = \App\Models\TahunPelajaran::orderBy('nm_tp', 'desc')->get();

        // Get active TP from Settings, fallback to is_active
        $activeTpId = \App\Models\Setting::get('active_academic_year');
        $tpAktif = $activeTpId ? \App\Models\TahunPelajaran::find($activeTpId) : null;
        if (!$tpAktif) {
            $tpAktif = \App\Models\TahunPelajaran::where('is_active', true)->first();
        }
        if (!$tpId && $tpAktif) {
            $tpId = $tpAktif->id;
        }

        // Build pelanggaran query - GROUP BY STUDENT
        $pelanggaranQuery = \App\Models\PdsPelanggaran::with(['student.kelas']);

        if ($kelasId) {
            $pelanggaranQuery->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($tpId) {
            $pelanggaranQuery->where('tp_id', $tpId);
        }

        // Group pelanggaran by student
        $pelanggaranAll = $pelanggaranQuery->get();
        $pelanggaranCollection = $pelanggaranAll->groupBy('student_id')->map(function ($items, $studentId) {
            $student = $items->first()->student;
            $latestStatus = $items->sortByDesc('tanggal')->first()->status ?? 'pending';
            return [
                'student' => $student,
                'encrypted_id' => encrypt($studentId),
                'total_poin' => $items->sum('poin'),
                'jumlah' => $items->count(),
                'latest_date' => $items->max('tanggal'),
                'status' => $latestStatus,
            ];
        })->sortByDesc('jumlah')->values();

        // Paginate pelanggaran
        $pelanggaranPage = $request->get('page_pelanggaran', 1);
        $pelanggaranPerPage = 10;
        $pelanggaranGrouped = new LengthAwarePaginator(
            $pelanggaranCollection->forPage($pelanggaranPage, $pelanggaranPerPage),
            $pelanggaranCollection->count(),
            $pelanggaranPerPage,
            $pelanggaranPage,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'page_pelanggaran']
        );

        // Build konseling query - GROUP BY STUDENT
        $konselingQuery = \App\Models\PdsKonseling::with(['student.kelas']);

        if ($kelasId) {
            $konselingQuery->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($tpId) {
            $konselingQuery->where('tp_id', $tpId);
        }

        // Group konseling by student
        $konselingAll = $konselingQuery->get();
        $konselingCollection = $konselingAll->groupBy('student_id')->map(function ($items, $studentId) {
            $student = $items->first()->student;
            $latestStatus = $items->sortByDesc('tanggal')->first()->status ?? 'pending';
            return [
                'student' => $student,
                'encrypted_id' => encrypt($studentId),
                'jumlah' => $items->count(),
                'latest_date' => $items->max('tanggal'),
                'status' => $latestStatus,
            ];
        })->sortByDesc('jumlah')->values();

        // Paginate konseling
        $konselingPage = $request->get('page_konseling', 1);
        $konselingPerPage = 10;
        $konselingGrouped = new LengthAwarePaginator(
            $konselingCollection->forPage($konselingPage, $konselingPerPage),
            $konselingCollection->count(),
            $konselingPerPage,
            $konselingPage,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'page_konseling']
        );

        // Build terlambat query - GROUP BY STUDENT
        $terlambatQuery = \App\Models\SiswaTerlambat::with(['student.kelas']);

        if ($kelasId) {
            $terlambatQuery->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($tpId) {
            $terlambatQuery->where('tp_id', $tpId);
        }

        if ($tanggalTerlambat) {
            $terlambatQuery->whereDate('tanggal', $tanggalTerlambat);
        }

        // Group terlambat by student
        $terlambatAll = $terlambatQuery->get();
        $terlambatCollection = $terlambatAll->groupBy('student_id')->map(function ($items, $studentId) {
            $student = $items->first()->student;
            $latestStatus = $items->sortByDesc('tanggal')->first()->status ?? 'pending';
            return [
                'student' => $student,
                'encrypted_id' => encrypt($studentId),
                'total_menit' => $items->sum('keterlambatan_menit'),
                'jumlah' => $items->count(),
                'latest_date' => $items->max('tanggal'),
                'status' => $latestStatus,
            ];
        })->sortByDesc('jumlah')->values();

        // Paginate terlambat
        $terlambatPage = $request->get('page_terlambat', 1);
        $terlambatPerPage = 10;
        $terlambatGrouped = new LengthAwarePaginator(
            $terlambatCollection->forPage($terlambatPage, $terlambatPerPage),
            $terlambatCollection->count(),
            $terlambatPerPage,
            $terlambatPage,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'page_terlambat']
        );

        // Statistics (filtered by TP if selected)
        $pelanggaranStatsQuery = \App\Models\PdsPelanggaran::query();
        $konselingStatsQuery = \App\Models\PdsKonseling::query();
        $terlambatStatsQuery = \App\Models\SiswaTerlambat::query();

        if ($tpId) {
            $pelanggaranStatsQuery->where('tp_id', $tpId);
            $konselingStatsQuery->where('tp_id', $tpId);
            $terlambatStatsQuery->where('tp_id', $tpId);
        }

        $stats = [
            'total_pelanggaran' => (clone $pelanggaranStatsQuery)->count(),
            'total_konseling' => (clone $konselingStatsQuery)->count(),
            'total_terlambat' => (clone $terlambatStatsQuery)->count(),
            'pelanggaran_pending' => (clone $pelanggaranStatsQuery)->where('status', 'pending')->count(),
            'konseling_pending' => (clone $konselingStatsQuery)->where('status', 'pending')->count(),
            'terlambat_pending' => (clone $terlambatStatsQuery)->where('status', 'pending')->count(),
        ];

        // Trend data for chart (Semester Genap: Jan - Jun)
        $currentYear = now()->year;
        $semesterStart = Carbon::createFromDate($currentYear, 1, 1)->startOfWeek();
        $semesterEnd = Carbon::createFromDate($currentYear, 6, 30)->endOfWeek();

        $lateTrendData = [];
        $violationTrendData = [];
        $counselingTrendData = [];

        $weekStart = $semesterStart->copy();
        $weekNumber = 1;

        while ($weekStart->lte($semesterEnd)) {
            $weekEnd = $weekStart->copy()->endOfWeek();
            $monthName = $weekStart->translatedFormat('M');

            // Late count for this week
            $lateQuery = \App\Models\SiswaTerlambat::whereBetween('tanggal', [$weekStart, $weekEnd]);
            if ($tpId) {
                $lateQuery->where('tp_id', $tpId);
            }
            if ($kelasId) {
                $lateQuery->whereHas('student', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                });
            }
            $lateCount = $lateQuery->count();

            // Violation count for this week
            $violationQuery = \App\Models\PdsPelanggaran::whereBetween('tanggal', [$weekStart, $weekEnd]);
            if ($tpId) {
                $violationQuery->where('tp_id', $tpId);
            }
            if ($kelasId) {
                $violationQuery->whereHas('student', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                });
            }
            $violationCount = $violationQuery->count();

            // Counseling count for this week
            $counselingQuery = \App\Models\PdsKonseling::whereBetween('tanggal', [$weekStart, $weekEnd]);
            if ($tpId) {
                $counselingQuery->where('tp_id', $tpId);
            }
            if ($kelasId) {
                $counselingQuery->whereHas('student', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                });
            }
            $counselingCount = $counselingQuery->count();

            $lateTrendData[] = [
                'week' => 'Minggu ' . $weekNumber,
                'date' => $weekStart->format('d M'),
                'count' => $lateCount,
                'month' => $monthName,
            ];

            $violationTrendData[] = [
                'week' => 'Minggu ' . $weekNumber,
                'date' => $weekStart->format('d M'),
                'count' => $violationCount,
            ];

            $counselingTrendData[] = [
                'week' => 'Minggu ' . $weekNumber,
                'date' => $weekStart->format('d M'),
                'count' => $counselingCount,
            ];

            $weekStart->addWeek();
            $weekNumber++;
        }

        return view('frontend.kesiswaan', compact(
            'pelanggaranGrouped',
            'konselingGrouped',
            'terlambatGrouped',
            'kelasList',
            'kelasId',
            'tpList',
            'tpId',
            'tanggalTerlambat',
            'stats',
            'lateTrendData',
            'violationTrendData',
            'counselingTrendData'
        ));
    }

    /**
     * Display pelanggaran detail for a specific student.
     */
    public function kesiswaanPelanggaranDetail($encryptedId)
    {
        try {
            $studentId = decrypt($encryptedId);
        } catch (\Exception $e) {
            abort(404, 'Data tidak ditemukan');
        }

        $student = Student::with('kelas')->findOrFail($studentId);
        $pelanggarans = \App\Models\PdsPelanggaran::where('student_id', $studentId)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPoin = $pelanggarans->sum('poin');

        return view('frontend.kesiswaan.pelanggaran-detail', compact('student', 'pelanggarans', 'totalPoin', 'encryptedId'));
    }

    /**
     * Display konseling detail for a specific student.
     */
    public function kesiswaanKonselingDetail($encryptedId)
    {
        try {
            $studentId = decrypt($encryptedId);
        } catch (\Exception $e) {
            abort(404, 'Data tidak ditemukan');
        }

        $student = Student::with('kelas')->findOrFail($studentId);
        $konselings = \App\Models\PdsKonseling::where('student_id', $studentId)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('frontend.kesiswaan.konseling-detail', compact('student', 'konselings', 'encryptedId'));
    }

    /**
     * Display terlambat detail for a specific student.
     */
    public function kesiswaanTerlambatDetail($encryptedId)
    {
        try {
            $studentId = decrypt($encryptedId);
        } catch (\Exception $e) {
            abort(404, 'Data tidak ditemukan');
        }

        $student = Student::with('kelas')->findOrFail($studentId);
        $terlambats = \App\Models\SiswaTerlambat::where('student_id', $studentId)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalMenit = $terlambats->sum('keterlambatan_menit');

        return view('frontend.kesiswaan.terlambat-detail', compact('student', 'terlambats', 'totalMenit', 'encryptedId'));
    }
}
