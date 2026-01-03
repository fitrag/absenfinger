<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\Walas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display attendance summary per class.
     */
    public function index(Request $request)
    {
        // Filter by date
        $date = $request->get('date', now()->toDateString());

        // Check user role and level
        $userRoles = session('user_roles', []);
        $userLevel = session('user_level', '');
        $userId = session('user_id');
        $isWaliKelas = in_array('Wali Kelas', $userRoles);
        $isAdmin = in_array('Admin', $userRoles) || in_array('Kepsek', $userRoles) || $userLevel === 'admin';
        $isPiket = in_array('Piket', $userRoles);

        // Can input/update absence
        $canInputAbsence = $isAdmin || $isPiket;

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
                }
            }
        }

        // Get kelas list for display (filtered for Wali Kelas, exclude '-' kelas)
        if ($isWaliKelas && !$isAdmin && $walasKelasId) {
            $kelasList = Kelas::where('id', $walasKelasId)->where('nm_kls', '!=', '-')->orderBy('nm_kls')->get();
        } else {
            $kelasList = Kelas::where('nm_kls', '!=', '-')->orderBy('nm_kls')->get();
        }

        // Get all active students grouped by kelas
        $allStudents = Student::where('is_active', true)->get()->groupBy('kelas_id');

        // Get all attendance data for the date
        $allAttendanceData = Attendance::whereDate('checktime', $date)->get()->groupBy('nis');

        // Map NIS to kelas_id for faster lookup
        $nisToKelas = Student::where('is_active', true)->pluck('kelas_id', 'nis');

        // Calculate attendance stats per class
        $kelasAttendance = [];
        $totalHadir = 0;
        $totalSakit = 0;
        $totalIzin = 0;
        $totalAlpha = 0;
        $totalBolos = 0;
        $totalTerlambat = 0;
        $totalTidakAbsen = 0;
        $totalSiswa = 0;

        foreach ($kelasList as $kelas) {
            $studentsInClass = $allStudents->get($kelas->id, collect([]));
            $studentCount = $studentsInClass->count();
            $totalSiswa += $studentCount;

            $hadir = 0;
            $sakit = 0;
            $izin = 0;
            $alpha = 0;
            $bolos = 0;
            $terlambat = 0;

            foreach ($studentsInClass as $student) {
                $records = $allAttendanceData->get($student->nis);

                if (!$records) {
                    // No attendance record = tidak absen
                    continue;
                }

                // Check for special status first (Sakit/Izin/Alpha)
                $specialRecord = $records->whereIn('checktype', [2, 3, 4])->first();
                if ($specialRecord) {
                    if ($specialRecord->checktype == 2) {
                        $sakit++;
                    } elseif ($specialRecord->checktype == 3) {
                        $izin++;
                    } elseif ($specialRecord->checktype == 4) {
                        $alpha++;
                    }
                    continue;
                }

                // Check for normal attendance
                $checkIn = $records->where('checktype', 0)->sortBy('checktime')->first();
                $checkOut = $records->where('checktype', 1)->sortByDesc('checktime')->first();

                if ($checkIn) {
                    $isLate = $checkIn->checktime->format('H:i') > '07:00';
                    if (!$checkOut) {
                        $bolos++;
                    } elseif ($isLate) {
                        $terlambat++;
                    } else {
                        $hadir++;
                    }
                }
            }

            $tidakAbsen = $studentCount - ($hadir + $sakit + $izin + $alpha + $bolos + $terlambat);

            $kelasAttendance[] = [
                'id' => $kelas->id,
                'nama_kelas' => $kelas->nm_kls,
                'jumlah_siswa' => $studentCount,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'bolos' => $bolos,
                'terlambat' => $terlambat,
                'tidak_absen' => $tidakAbsen,
            ];

            $totalHadir += $hadir;
            $totalSakit += $sakit;
            $totalIzin += $izin;
            $totalAlpha += $alpha;
            $totalBolos += $bolos;
            $totalTerlambat += $terlambat;
            $totalTidakAbsen += $tidakAbsen;
        }

        // Global statistics
        $totalStudents = $totalSiswa;
        $hadirCount = $totalHadir;
        $sakitCount = $totalSakit;
        $izinCount = $totalIzin;
        $alphaCount = $totalAlpha;
        $bolosCount = $totalBolos;
        $terlambatCount = $totalTerlambat;
        $belumAbsenCount = $totalTidakAbsen;

        return view('admin.attendance.index', compact(
            'kelasAttendance',
            'kelasList',
            'date',
            'totalStudents',
            'hadirCount',
            'terlambatCount',
            'bolosCount',
            'alphaCount',
            'sakitCount',
            'izinCount',
            'belumAbsenCount',
            'isWaliKelas',
            'isAdmin',
            'walasKelasInfo',
            'canInputAbsence'
        ));
    }

    /**
     * Display attendance detail for a specific class.
     */
    public function showByKelas(Request $request, $kelasId)
    {
        $date = $request->get('date', now()->toDateString());

        $kelas = Kelas::findOrFail($kelasId);

        // Get students in this class
        $students = Student::where('kelas_id', $kelasId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get attendance data for the date
        $attendanceData = Attendance::whereDate('checktime', $date)
            ->get()
            ->groupBy('nis')
            ->map(function ($records) {
                $checkIn = $records->where('checktype', 0)->sortBy('checktime')->first();
                $special = $records->whereIn('checktype', [2, 3, 4])->first();
                if (!$checkIn && $special) {
                    $checkIn = $special;
                }
                return [
                    'check_in' => $checkIn,
                    'check_out' => $records->where('checktype', 1)->sortByDesc('checktime')->first(),
                ];
            });

        // Get kelas list for filter dropdown
        $kelasList = Kelas::orderBy('nm_kls')->get();

        return view('admin.attendance.show-kelas', compact(
            'kelas',
            'students',
            'attendanceData',
            'date',
            'kelasList'
        ));
    }

    /**
     * Delete attendance record.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Export attendance to CSV.
     */
    /**
     * Export attendance to Excel.
     */
    public function export(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $kelasId = $request->get('kelas_id');

        // Get All Students (filtered by class if needed)
        $studentQuery = Student::with('kelas')->where('is_active', true)->orderBy('name');
        if ($kelasId) {
            $studentQuery->where('kelas_id', $kelasId);
        }
        $students = $studentQuery->get();

        // Get Attendance Data for the date
        $allAttendances = Attendance::whereDate('checktime', $date)->get()->groupBy('nis');

        $kelasName = 'Semua Kelas';
        if ($kelasId) {
            $kelas = Kelas::find($kelasId);
            if ($kelas) {
                $kelasName = $kelas->nm_kls;
            }
        }

        $data = [
            ['Data Absensi Siswa'],
            ['Tanggal', Carbon::parse($date)->format('d F Y')],
            ['Kelas', $kelasName],
            [],
            ['No', 'NIS', 'Nama', 'Kelas', 'Jam Masuk', 'Jam Pulang', 'Status']
        ];

        foreach ($students as $index => $student) {
            $records = $allAttendances->get($student->nis, collect([]));

            // Logic similar to IndexController to determine status
            $checkIn = $records->where('checktype', 0)->sortBy('checktime')->first();
            $checkOut = $records->where('checktype', 1)->sortByDesc('checktime')->first();
            $special = $records->whereIn('checktype', [2, 3, 4])->first(); // 2=Sakit, 3=Izin, 4=Alpha

            $jamMasuk = '-';
            $jamPulang = '-';
            $status = 'Tidak Hadir';

            if ($special) {
                if ($special->checktype == 2)
                    $status = 'Sakit';
                elseif ($special->checktype == 3)
                    $status = 'Izin';
                elseif ($special->checktype == 4)
                    $status = 'Alpha';
            } elseif ($checkIn) {
                $jamMasuk = $checkIn->checktime->format('H:i:s');
                $isLate = $checkIn->checktime->format('H:i') > '07:00';

                if ($checkOut) {
                    $jamPulang = $checkOut->checktime->format('H:i:s');
                    $status = $isLate ? 'Terlambat' : 'Hadir';
                } else {
                    $status = 'Bolos'; // Masuk tapi tidak pulang
                }
            } else {
                // Check if purely Alpha/Absent (already set to default 'Tidak Hadir')
            }

            $data[] = [
                $index + 1,
                $student->nis,
                $student->name,
                $student->kelas->nm_kls ?? '-',
                $jamMasuk,
                $jamPulang,
                $status
            ];
        }

        $filename = "Absensi_" . ($kelasId ? "Kelas_{$kelasName}_" : "") . "{$date}.xlsx";

        // Clean filename
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename);

        return \Shuchkin\SimpleXLSXGen::fromArray($data)->downloadAs($filename);
    }

    /**
     * Get students by class who haven't checked in yet.
     */
    public function getStudentsByClass(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $date = $request->get('date', now()->toDateString());

        if (!$kelasId) {
            return response()->json([]);
        }

        // Get students who have already checked in today
        $checkedInNis = Attendance::whereDate('checktime', $date)
            ->pluck('nis')
            ->unique()
            ->toArray();

        // Get students from the class who haven't checked in
        $students = Student::where('kelas_id', $kelasId)
            ->where('is_active', true)
            ->whereNotIn('nis', $checkedInNis)
            ->orderBy('name')
            ->get(['id', 'nis', 'name']);

        return response()->json($students);
    }

    /**
     * Get students with absence status (sakit/izin/alpha/bolos) for a given class and date.
     */
    public function getAbsentStudents(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $date = $request->get('date', now()->toDateString());

        if (!$kelasId) {
            return response()->json([]);
        }

        // Get all attendance records for the given date
        $allAttendances = Attendance::whereDate('checktime', $date)->get();

        // Group by NIS
        $attendanceByNis = $allAttendances->groupBy('nis');

        // Get students from the specified class
        $students = Student::where('kelas_id', $kelasId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'nis', 'name']);

        $result = [];

        foreach ($students as $student) {
            $records = $attendanceByNis->get($student->nis);

            if (!$records) {
                continue;
            }

            // Check for special status (Sakit/Izin/Alpha) - checktype 2, 3, 4
            $specialRecord = $records->whereIn('checktype', [2, 3, 4])->first();
            if ($specialRecord) {
                $statusMap = [2 => 'sakit', 3 => 'izin', 4 => 'alpha'];
                $result[] = [
                    'id' => $student->id,
                    'nis' => $student->nis,
                    'name' => $student->name,
                    'attendance_id' => $specialRecord->id,
                    'current_status' => $statusMap[$specialRecord->checktype] ?? 'unknown',
                ];
                continue;
            }

            // Check for Bolos (check-in exists but no check-out)
            $checkIn = $records->where('checktype', 0)->first();
            $checkOut = $records->where('checktype', 1)->first();

            if ($checkIn && !$checkOut) {
                $result[] = [
                    'id' => $student->id,
                    'nis' => $student->nis,
                    'name' => $student->name,
                    'attendance_id' => $checkIn->id,
                    'current_status' => 'bolos',
                ];
            }
        }

        return response()->json($result);
    }

    /**
     * Store absence record (sakit/izin/alpha/hadir).
     */
    public function storeAbsence(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'students' => 'required|array|min:1',
            'students.*.nis' => 'required|string|exists:students,nis',
            'students.*.status' => 'nullable|in:hadir,sakit,izin,alpha',
        ]);

        $date = $request->date;
        $students = $request->students;

        // Map status to checktype
        $checktypeMap = [
            'sakit' => 2,
            'izin' => 3,
            'alpha' => 4,
        ];

        $count = 0;
        foreach ($students as $studentData) {
            $nis = $studentData['nis'];
            $status = $studentData['status'] ?? null;

            // Skip if no status selected
            if (empty($status)) {
                continue;
            }

            // Check if already has an attendance record for this date
            $exists = Attendance::where('nis', $nis)
                ->whereDate('checktime', $date)
                ->exists();

            if (!$exists) {
                if ($status === 'hadir') {
                    // Create check-in record (hadir) at 07:00
                    Attendance::create([
                        'nis' => $nis,
                        'checktime' => Carbon::parse($date)->setTime(7, 0, 0),
                        'checktype' => 0, // Check-in
                    ]);
                    // Create check-out record at 16:00
                    Attendance::create([
                        'nis' => $nis,
                        'checktime' => Carbon::parse($date)->setTime(16, 0, 0),
                        'checktype' => 1, // Check-out
                    ]);
                } else {
                    $checktype = $checktypeMap[$status];
                    Attendance::create([
                        'nis' => $nis,
                        'checktime' => Carbon::parse($date)->setTime(0, 0, 0),
                        'checktype' => $checktype,
                    ]);
                }
                $count++;
            }
        }

        return redirect()->route('admin.attendance.index', ['date' => $date])
            ->with('success', "Berhasil menyimpan {$count} data kehadiran.");
    }

    /**
     * Update absence records (sakit/izin/alpha).
     */
    public function updateAbsence(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'students' => 'nullable|array',
            'students.*.attendance_id' => 'required|exists:attendances,id',
            'students.*.status' => 'nullable|in:sakit,izin,alpha,delete',
        ]);

        $date = $request->date;
        $students = $request->students ?? [];

        // Map status to checktype
        $checktypeMap = [
            'sakit' => 2,
            'izin' => 3,
            'alpha' => 4,
        ];

        $updateCount = 0;
        $deleteCount = 0;

        foreach ($students as $studentData) {
            $attendanceId = $studentData['attendance_id'];
            $status = $studentData['status'] ?? null;

            // Skip if no status selected
            if (empty($status)) {
                continue;
            }

            $attendance = Attendance::find($attendanceId);
            if (!$attendance) {
                continue;
            }

            if ($status === 'delete') {
                $attendance->delete();
                $deleteCount++;
            } else {
                $attendance->update([
                    'checktype' => $checktypeMap[$status],
                ]);
                $updateCount++;
            }
        }

        $message = "";
        if ($updateCount > 0) {
            $message .= "Berhasil mengupdate {$updateCount} data. ";
        }
        if ($deleteCount > 0) {
            $message .= "Berhasil menghapus {$deleteCount} data.";
        }

        if (empty($message)) {
            $message = "Tidak ada perubahan data.";
        }

        return redirect()->route('admin.attendance.index', ['date' => $date])
            ->with('success', trim($message));
    }

    /**
     * Print attendance PDF for a class.
     */
    public function printPdf(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $kelasId = $request->get('kelas_id');

        if (!$kelasId) {
            return redirect()->route('admin.attendance.index', ['date' => $date])
                ->with('error', 'Pilih kelas terlebih dahulu untuk mencetak.');
        }

        $kelas = Kelas::find($kelasId);
        if (!$kelas) {
            return redirect()->route('admin.attendance.index', ['date' => $date])
                ->with('error', 'Kelas tidak ditemukan.');
        }

        // Get students in this class
        $students = Student::where('kelas_id', $kelasId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get attendance data for the date
        $attendanceData = Attendance::whereDate('checktime', $date)
            ->get()
            ->groupBy('nis')
            ->map(function ($records) {
                $checkIn = $records->where('checktype', 0)->sortBy('checktime')->first();
                if (!$checkIn) {
                    $checkIn = $records->whereIn('checktype', [2, 3, 4])->first();
                }
                return [
                    'check_in' => $checkIn,
                    'check_out' => $records->where('checktype', 1)->sortByDesc('checktime')->first(),
                ];
            });

        // Get settings for school info
        $settings = \App\Models\Setting::first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.attendance.print-pdf', compact(
            'students',
            'attendanceData',
            'kelas',
            'date',
            'settings'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("absensi_kelas_{$kelas->nm_kls}_{$date}.pdf");
    }
}
