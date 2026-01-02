<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\Walas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display period attendance report (from date to date).
     */
    public function daily(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->get('end_date', now()->endOfWeek()->toDateString());

        // Get default kelas (X DKV 1 or first kelas)
        $defaultKelas = Kelas::where('nm_kls', 'like', '%X DKV 1%')->first();
        if (!$defaultKelas) {
            $defaultKelas = Kelas::orderBy('nm_kls')->first();
        }
        $kelasId = $request->get('kelas_id', $defaultKelas?->id);

        // Check user role
        $userRoles = session('user_roles', []);
        $userId = session('user_id');
        $isWaliKelas = in_array('Wali Kelas', $userRoles);
        $isAdmin = in_array('Admin', $userRoles) || in_array('Kepsek', $userRoles);

        $walasKelasId = null;
        $walasKelasInfo = null;

        // If Wali Kelas, get their assigned class
        if ($isWaliKelas && !$isAdmin) {
            $guru = \App\Models\Guru::where('user_id', $userId)->first();
            if ($guru) {
                $walas = Walas::with('kelas')
                    ->where('guru_id', $guru->id)
                    ->where('is_active', true)
                    ->first();
                if ($walas) {
                    $walasKelasId = $walas->kelas_id;
                    $walasKelasInfo = $walas->kelas;
                    $kelasId = $walasKelasId; // Force to their class
                }
            }
        }

        // Parse dates
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Build date list (excluding weekends)
        $dates = [];
        $current = $start->copy();
        while ($current <= $end) {
            // Skip weekends
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                $dates[] = $current->copy();
            }
            $current->addDay();
        }
        $totalDays = count($dates);

        // Build query for students
        $query = Student::with(['kelas'])->where('is_active', true);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $students = $query->orderBy('name')->get();

        // Get all attendance records for the period
        $attendances = Attendance::whereBetween('checktime', [$start->startOfDay(), $end->endOfDay()])->get();

        // Group by NIS and date
        $attendanceByNisDate = $attendances->groupBy(function ($item) {
            return $item->nis . '_' . $item->checktime->format('Y-m-d');
        });

        // Calculate attendance per student per day
        $attendanceMatrix = [];
        $summaryData = [];

        foreach ($students as $student) {
            $attendanceMatrix[$student->nis] = [];
            $summary = [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpha' => 0,
                'bolos' => 0,
            ];

            foreach ($dates as $date) {
                $key = $student->nis . '_' . $date->format('Y-m-d');
                $records = $attendanceByNisDate->get($key);

                $status = '-'; // Default: belum absen

                if ($records) {
                    $checkIn = $records->where('checktype', 0)->first();
                    $checkOut = $records->where('checktype', 1)->first();
                    $specialStatus = $records->whereIn('checktype', [2, 3, 4])->first();

                    if ($specialStatus) {
                        if ($specialStatus->checktype == 2) {
                            $status = 'S';
                            $summary['sakit']++;
                        } elseif ($specialStatus->checktype == 3) {
                            $status = 'I';
                            $summary['izin']++;
                        } elseif ($specialStatus->checktype == 4) {
                            $status = 'A';
                            $summary['alpha']++;
                        }
                    } elseif ($checkIn) {
                        if (!$checkOut) {
                            $status = 'B';
                            $summary['bolos']++;
                        } else {
                            $status = 'H';
                            $summary['hadir']++;
                        }
                    }
                }

                $attendanceMatrix[$student->nis][$date->format('Y-m-d')] = $status;
            }

            // Calculate attendance percentage
            $totalAbsent = $summary['sakit'] + $summary['izin'] + $summary['alpha'] + $summary['bolos'];
            $summary['total_absent'] = $totalAbsent;
            $summary['percentage'] = $totalDays > 0 ? round(($summary['hadir'] / $totalDays) * 100, 1) : 0;

            $summaryData[$student->nis] = $summary;
        }

        // Get kelas list for filter
        if ($isWaliKelas && !$isAdmin && $walasKelasId) {
            // Wali Kelas only sees their class
            $kelasList = Kelas::where('id', $walasKelasId)->get();
        } else {
            // Admin sees all
            $kelasList = Kelas::orderBy('nm_kls')->get();
        }

        return view('admin.reports.daily', compact(
            'students',
            'attendanceMatrix',
            'summaryData',
            'dates',
            'kelasList',
            'startDate',
            'endDate',
            'kelasId',
            'totalDays',
            'isWaliKelas',
            'isAdmin',
            'walasKelasInfo'
        ));
    }

    /**
     * Display monthly attendance report (period of months).
     */
    public function monthly(Request $request)
    {
        $startMonth = $request->get('start_month', now()->startOfMonth()->format('Y-m'));
        $endMonth = $request->get('end_month', now()->format('Y-m'));

        // Get default kelas (X DKV 1 or first kelas)
        $defaultKelas = Kelas::where('nm_kls', 'like', '%X DKV 1%')->first();
        if (!$defaultKelas) {
            $defaultKelas = Kelas::orderBy('nm_kls')->first();
        }
        $kelasId = $request->get('kelas_id', $defaultKelas?->id);

        // Check user role
        $userRoles = session('user_roles', []);
        $userId = session('user_id');
        $isWaliKelas = in_array('Wali Kelas', $userRoles);
        $isAdmin = in_array('Admin', $userRoles) || in_array('Kepsek', $userRoles);

        $walasKelasId = null;
        $walasKelasInfo = null;

        // If Wali Kelas, get their assigned class
        if ($isWaliKelas && !$isAdmin) {
            $guru = \App\Models\Guru::where('user_id', $userId)->first();
            if ($guru) {
                $walas = Walas::with('kelas')
                    ->where('guru_id', $guru->id)
                    ->where('is_active', true)
                    ->first();
                if ($walas) {
                    $walasKelasId = $walas->kelas_id;
                    $walasKelasInfo = $walas->kelas;
                    $kelasId = $walasKelasId; // Force to their class
                }
            }
        }

        // Parse months
        $startDate = Carbon::parse($startMonth . '-01')->startOfMonth();
        $endDate = Carbon::parse($endMonth . '-01')->endOfMonth();

        // Calculate total working days (excluding weekends)
        $totalDays = 0;
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                $totalDays++;
            }
            $current->addDay();
        }

        // Build query for students
        $query = Student::with(['kelas'])->where('is_active', true);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $students = $query->orderBy('name')->get();

        // Get all attendance records for the period
        $attendances = Attendance::whereBetween('checktime', [$startDate->startOfDay(), $endDate->endOfDay()])->get();

        // Group by NIS and date
        $attendanceByNisDate = $attendances->groupBy(function ($item) {
            return $item->nis . '_' . $item->checktime->format('Y-m-d');
        });

        // Calculate summary per student
        $monthlySummary = [];
        foreach ($students as $student) {
            $summary = [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpha' => 0,
                'bolos' => 0,
            ];

            $currentDay = $startDate->copy();
            while ($currentDay <= $endDate) {
                // Skip weekends (Saturday = 6, Sunday = 0)
                if ($currentDay->dayOfWeek === 0 || $currentDay->dayOfWeek === 6) {
                    $currentDay->addDay();
                    continue;
                }

                $key = $student->nis . '_' . $currentDay->format('Y-m-d');
                $records = $attendanceByNisDate->get($key);

                if ($records) {
                    $checkIn = $records->where('checktype', 0)->first();
                    $checkOut = $records->where('checktype', 1)->first();
                    $specialStatus = $records->whereIn('checktype', [2, 3, 4])->first();

                    if ($specialStatus) {
                        if ($specialStatus->checktype == 2) {
                            $summary['sakit']++;
                        } elseif ($specialStatus->checktype == 3) {
                            $summary['izin']++;
                        } elseif ($specialStatus->checktype == 4) {
                            $summary['alpha']++;
                        }
                    } elseif ($checkIn) {
                        if (!$checkOut) {
                            $summary['bolos']++;
                        } else {
                            $summary['hadir']++;
                        }
                    }
                }

                $currentDay->addDay();
            }

            // Calculate attendance percentage based on total days that have any attendance data
            $totalRecorded = $summary['hadir'] + $summary['sakit'] + $summary['izin'] + $summary['alpha'] + $summary['bolos'];
            $totalAbsent = $summary['sakit'] + $summary['izin'] + $summary['alpha'] + $summary['bolos'];
            $summary['total_absent'] = $totalAbsent;
            $summary['percentage'] = $totalRecorded > 0 ? round(($summary['hadir'] / $totalRecorded) * 100, 1) : 0;

            $monthlySummary[$student->nis] = $summary;
        }

        // Get kelas list for filter
        if ($isWaliKelas && !$isAdmin && $walasKelasId) {
            // Wali Kelas only sees their class
            $kelasList = Kelas::where('id', $walasKelasId)->get();
        } else {
            // Admin sees all
            $kelasList = Kelas::orderBy('nm_kls')->get();
        }

        return view('admin.reports.monthly', compact(
            'students',
            'monthlySummary',
            'kelasList',
            'startMonth',
            'endMonth',
            'kelasId',
            'startDate',
            'endDate',
            'totalDays',
            'isWaliKelas',
            'isAdmin',
            'walasKelasInfo'
        ));
    }

    /**
     * Calculate attendance statistics.
     */
    private function calculateStats($students, $attendanceData)
    {
        $stats = [
            'total' => $students->count(),
            'hadir' => 0,
            'terlambat' => 0,
            'bolos' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
            'belum_absen' => 0,
        ];

        foreach ($students as $student) {
            $data = $attendanceData->get($student->nis);

            if (!$data) {
                $stats['belum_absen']++;
                continue;
            }

            $checkIn = $data['check_in'];
            $checkOut = $data['check_out'];

            if ($checkIn) {
                if ($checkIn->checktype == 2) {
                    $stats['sakit']++;
                } elseif ($checkIn->checktype == 3) {
                    $stats['izin']++;
                } elseif ($checkIn->checktype == 4) {
                    $stats['alpha']++;
                } else {
                    $isLate = $checkIn->checktime->format('H:i') > '07:00';
                    if (!$checkOut) {
                        $stats['bolos']++;
                    } elseif ($isLate) {
                        $stats['terlambat']++;
                    } else {
                        $stats['hadir']++;
                    }
                }
            }
        }

        return $stats;
    }

    /**
     * Get Indonesian day abbreviation.
     */
    public static function getIndonesianDay($dayOfWeek)
    {
        $days = [
            0 => 'Mg', // Minggu
            1 => 'Sn', // Senin
            2 => 'Sl', // Selasa
            3 => 'Rb', // Rabu
            4 => 'Km', // Kamis
            5 => 'Jm', // Jumat
            6 => 'Sb', // Sabtu
        ];

        return $days[$dayOfWeek] ?? '';
    }

    /**
     * Export daily report to PDF.
     */
    public function dailyPdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->get('end_date', now()->endOfWeek()->toDateString());
        $kelasId = $request->get('kelas_id');
        $paperSize = $request->get('paper_size', 'a4');
        $orientation = $request->get('orientation', 'landscape');

        // Parse dates
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Build date list (excluding weekends)
        $dates = [];
        $current = $start->copy();
        while ($current <= $end) {
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                $dates[] = $current->copy();
            }
            $current->addDay();
        }
        $totalDays = count($dates);

        // Build query for students
        $query = Student::with(['kelas'])->where('is_active', true);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $students = $query->orderBy('name')->get();

        // Get all attendance records for the period
        $attendances = Attendance::whereBetween('checktime', [$start->startOfDay(), $end->endOfDay()])->get();

        // Group by NIS and date
        $attendanceByNisDate = $attendances->groupBy(function ($item) {
            return $item->nis . '_' . $item->checktime->format('Y-m-d');
        });

        // Calculate attendance per student per day
        $attendanceMatrix = [];
        $summaryData = [];

        foreach ($students as $student) {
            $attendanceMatrix[$student->nis] = [];
            $summary = [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpha' => 0,
                'bolos' => 0,
            ];

            foreach ($dates as $date) {
                $key = $student->nis . '_' . $date->format('Y-m-d');
                $records = $attendanceByNisDate->get($key);

                $status = '-';

                if ($records) {
                    $checkIn = $records->where('checktype', 0)->first();
                    $checkOut = $records->where('checktype', 1)->first();
                    $specialStatus = $records->whereIn('checktype', [2, 3, 4])->first();

                    if ($specialStatus) {
                        if ($specialStatus->checktype == 2) {
                            $status = 'S';
                            $summary['sakit']++;
                        } elseif ($specialStatus->checktype == 3) {
                            $status = 'I';
                            $summary['izin']++;
                        } elseif ($specialStatus->checktype == 4) {
                            $status = 'A';
                            $summary['alpha']++;
                        }
                    } elseif ($checkIn) {
                        if (!$checkOut) {
                            $status = 'B';
                            $summary['bolos']++;
                        } else {
                            $status = 'H';
                            $summary['hadir']++;
                        }
                    }
                }

                $attendanceMatrix[$student->nis][$date->format('Y-m-d')] = $status;
            }

            $totalAbsent = $summary['sakit'] + $summary['izin'] + $summary['alpha'] + $summary['bolos'];
            $summary['total_absent'] = $totalAbsent;
            $summary['percentage'] = $totalDays > 0 ? round(($summary['hadir'] / $totalDays) * 100, 1) : 0;

            $summaryData[$student->nis] = $summary;
        }

        // Get kelas info for filename
        $kelasInfo = null;
        $kelasName = '';
        if ($kelasId) {
            $kelasInfo = Kelas::find($kelasId);
            $kelasName = $kelasInfo ? '_' . str_replace(' ', '_', $kelasInfo->nm_kls) : '';
        }

        // Get settings
        $settings = \App\Models\Setting::getAllSettings();

        // Get walas (wali kelas) for the selected kelas
        $walas = null;
        if ($kelasId) {
            $walas = Walas::with('guru')->where('kelas_id', $kelasId)->where('is_active', true)->first();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.daily-pdf', compact(
            'students',
            'attendanceMatrix',
            'summaryData',
            'dates',
            'startDate',
            'endDate',
            'totalDays',
            'kelasInfo',
            'settings',
            'walas'
        ));

        $pdf->setPaper($paperSize, $orientation);

        $filename = 'laporan_presensi_' . $startDate . '_sd_' . $endDate . $kelasName . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export monthly report to PDF.
     */
    public function monthlyPdf(Request $request)
    {
        $startMonth = $request->get('start_month', now()->startOfMonth()->format('Y-m'));
        $endMonth = $request->get('end_month', now()->format('Y-m'));
        $kelasId = $request->get('kelas_id');
        $paperSize = $request->get('paper_size', 'a4');
        $orientation = $request->get('orientation', 'portrait');

        // Parse months
        $startDate = Carbon::parse($startMonth . '-01')->startOfMonth();
        $endDate = Carbon::parse($endMonth . '-01')->endOfMonth();

        // Calculate total working days (excluding weekends)
        $totalDays = 0;
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) {
                $totalDays++;
            }
            $current->addDay();
        }

        // Build query for students
        $query = Student::with(['kelas'])->where('is_active', true);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $students = $query->orderBy('name')->get();

        // Get all attendance records for the period
        $attendances = Attendance::whereBetween('checktime', [$startDate->startOfDay(), $endDate->endOfDay()])->get();

        // Group by NIS and date
        $attendanceByNisDate = $attendances->groupBy(function ($item) {
            return $item->nis . '_' . $item->checktime->format('Y-m-d');
        });

        // Calculate summary per student
        $monthlySummary = [];
        foreach ($students as $student) {
            $summary = [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpha' => 0,
                'bolos' => 0,
            ];

            $currentDay = $startDate->copy();
            while ($currentDay <= $endDate) {
                // Skip weekends
                if ($currentDay->dayOfWeek === 0 || $currentDay->dayOfWeek === 6) {
                    $currentDay->addDay();
                    continue;
                }

                $key = $student->nis . '_' . $currentDay->format('Y-m-d');
                $records = $attendanceByNisDate->get($key);

                if ($records) {
                    $checkIn = $records->where('checktype', 0)->first();
                    $checkOut = $records->where('checktype', 1)->first();
                    $specialStatus = $records->whereIn('checktype', [2, 3, 4])->first();

                    if ($specialStatus) {
                        if ($specialStatus->checktype == 2) {
                            $summary['sakit']++;
                        } elseif ($specialStatus->checktype == 3) {
                            $summary['izin']++;
                        } elseif ($specialStatus->checktype == 4) {
                            $summary['alpha']++;
                        }
                    } elseif ($checkIn) {
                        if (!$checkOut) {
                            $summary['bolos']++;
                        } else {
                            $summary['hadir']++;
                        }
                    }
                }

                $currentDay->addDay();
            }

            // Calculate attendance percentage based on total days that have any attendance data
            $totalRecorded = $summary['hadir'] + $summary['sakit'] + $summary['izin'] + $summary['alpha'] + $summary['bolos'];
            $totalAbsent = $summary['sakit'] + $summary['izin'] + $summary['alpha'] + $summary['bolos'];
            $summary['total_absent'] = $totalAbsent;
            $summary['percentage'] = $totalRecorded > 0 ? round(($summary['hadir'] / $totalRecorded) * 100, 1) : 0;

            $monthlySummary[$student->nis] = $summary;
        }

        // Get kelas info
        $kelasInfo = null;
        if ($kelasId) {
            $kelasInfo = Kelas::find($kelasId);
        }

        // Get settings
        $settings = \App\Models\Setting::getAllSettings();

        // Get walas (wali kelas) for the selected kelas
        $walas = null;
        if ($kelasId) {
            $walas = Walas::with('guru')->where('kelas_id', $kelasId)->where('is_active', true)->first();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.monthly-pdf', compact(
            'students',
            'monthlySummary',
            'startMonth',
            'endMonth',
            'startDate',
            'endDate',
            'totalDays',
            'kelasInfo',
            'settings',
            'walas'
        ));

        $pdf->setPaper($paperSize, $orientation);

        $kelasName = $kelasInfo ? '_' . str_replace(' ', '_', $kelasInfo->nm_kls) : '';
        $filename = 'laporan_bulanan_' . $startMonth . '_sd_' . $endMonth . $kelasName . '.pdf';

        return $pdf->download($filename);
    }
}

