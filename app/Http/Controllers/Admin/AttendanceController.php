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
        $isKepsek = in_array('Kepsek', $userRoles);
        $isAdmin = in_array('Admin', $userRoles) || $isKepsek || $userLevel === 'admin';
        $isPiket = in_array('Piket', $userRoles);

        // Can input/update absence (Kepsek tidak bisa input/update)
        $canInputAbsence = (in_array('Admin', $userRoles) || $userLevel === 'admin' || $isPiket) && !$isKepsek;

        $walasKelasId = null;
        $walasKelasInfo = null;

        // Logika baru:
        // - Jika Piket (termasuk Piket+Walas) → tampilkan semua data seperti admin
        // - Jika Walas saja (tanpa Piket, bukan Admin) → filter berdasarkan kelas walas
        $shouldFilterByWalasKelas = $isWaliKelas && !$isAdmin && !$isPiket;

        if ($shouldFilterByWalasKelas) {
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

        // Get kelas list for display (filtered for Wali Kelas only, exclude '-' kelas)
        // Piket (termasuk Piket+Walas) dan Admin = lihat semua kelas
        // Walas saja (tanpa Piket) = hanya kelas walas
        if ($shouldFilterByWalasKelas && $walasKelasId) {
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
        $totalCheckin = 0;
        $totalCheckout = 0;
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
            $checkin = 0;
            $checkout = 0;

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
                    // Count as checked in
                    $checkin++;

                    // Count checkout
                    if ($checkOut) {
                        $checkout++;
                    }

                    // Check if this is PKL attendance (flexible hours)
                    $isPklAttendance = $checkIn->is_pkl ?? false;

                    if ($isPklAttendance) {
                        // PKL: hadir if both check-in and check-out exist, otherwise alpha
                        if ($checkOut) {
                            $hadir++;
                        } else {
                            $alpha++; // Only check-in, no check-out = alpha
                        }
                    } else {
                        // Regular attendance: jika ada checkIn dan checkOut = Hadir
                        if ($checkOut) {
                            $hadir++;
                        } else {
                            $bolos++;
                        }
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
                'checkin' => $checkin,
                'checkout' => $checkout,
            ];

            $totalHadir += $hadir;
            $totalSakit += $sakit;
            $totalIzin += $izin;
            $totalAlpha += $alpha;
            $totalBolos += $bolos;
            $totalTerlambat += $terlambat;
            $totalTidakAbsen += $tidakAbsen;
            $totalCheckin += $checkin;
            $totalCheckout += $checkout;
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
        $checkinCount = $totalCheckin;
        $checkoutCount = $totalCheckout;

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
            'checkinCount',
            'checkoutCount',
            'isWaliKelas',
            'isAdmin',
            'isKepsek',
            'isPiket',
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

        // Check user role
        $userRoles = session('user_roles', []);
        $userLevel = session('user_level', '');
        $isAdmin = in_array('Admin', $userRoles) || $userLevel === 'admin';
        $isPiket = in_array('Piket', $userRoles);
        $isWaliKelas = in_array('Wali Kelas', $userRoles);

        // Disable edit if Walas AND NOT Admin AND NOT Piket
        $disableEdit = $isWaliKelas && !$isAdmin && !$isPiket;

        return view('admin.attendance.show-kelas', compact(
            'kelas',
            'students',
            'attendanceData',
            'date',
            'kelasList',
            'disableEdit'
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
                $isPklAttendance = $checkIn->is_pkl ?? false;

                if ($checkOut) {
                    $jamPulang = $checkOut->checktime->format('H:i:s');
                    if ($isPklAttendance) {
                        // PKL: hadir if both check-in and check-out (no time check)
                        $status = 'Hadir';
                    } else {
                        // Regular: check for late
                        $isLate = $checkIn->checktime->format('H:i') > '07:00';
                        $status = $isLate ? 'Terlambat' : 'Hadir';
                    }
                } else {
                    // Only check-in, no check-out
                    $status = $isPklAttendance ? 'Alpha' : 'Bolos';
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

        // Save to temp file first to avoid output buffer issues
        $tempPath = storage_path('app/temp/' . $filename);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Generate and save Excel file
        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
        $xlsx->saveAs($tempPath);

        // Return as download and delete after sending
        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
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
            $existingRecords = Attendance::where('nis', $nis)
                ->whereDate('checktime', $date)
                ->get();

            if ($existingRecords->isEmpty()) {
                // No existing records - create new
                if ($status === 'hadir') {
                    // Use current time if today, otherwise use default times
                    $isToday = Carbon::parse($date)->isToday();
                    $checkInTime = $isToday ? now() : Carbon::parse($date)->setTime(7, 0, 0);
                    $checkOutTime = $isToday ? now() : Carbon::parse($date)->setTime(16, 0, 0);

                    // Create check-in record
                    Attendance::create([
                        'nis' => $nis,
                        'checktime' => $checkInTime,
                        'checktype' => 0, // Check-in
                    ]);
                    // Create check-out record
                    Attendance::create([
                        'nis' => $nis,
                        'checktime' => $checkOutTime,
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
            } else {
                // Record exists - handle based on status
                if ($status === 'hadir') {
                    // Delete any absence records (sakit=2, izin=3, alpha=4)
                    $deleted = Attendance::where('nis', $nis)
                        ->whereDate('checktime', $date)
                        ->whereIn('checktype', [2, 3, 4])
                        ->delete();

                    // Check if check-in and check-out exists
                    $hasCheckIn = $existingRecords->where('checktype', 0)->isNotEmpty();
                    $hasCheckOut = $existingRecords->where('checktype', 1)->isNotEmpty();

                    // Use current time if today, otherwise use default times
                    $isToday = Carbon::parse($date)->isToday();
                    $checkInTime = $isToday ? now() : Carbon::parse($date)->setTime(7, 0, 0);
                    $checkOutTime = $isToday ? now() : Carbon::parse($date)->setTime(16, 0, 0);

                    // Create check-in if not exists
                    if (!$hasCheckIn) {
                        Attendance::create([
                            'nis' => $nis,
                            'checktime' => $checkInTime,
                            'checktype' => 0, // Check-in
                        ]);
                    }

                    // Create check-out if not exists
                    if (!$hasCheckOut) {
                        Attendance::create([
                            'nis' => $nis,
                            'checktime' => $checkOutTime,
                            'checktype' => 1, // Check-out
                        ]);
                    }

                    if ($deleted > 0 || !$hasCheckIn || !$hasCheckOut) {
                        $count++;
                    }
                }
                // For other statuses (sakit/izin/alpha) when record exists, skip
            }
        }

        return redirect()->route('admin.attendance.index', ['date' => $date])
            ->with('success', "Berhasil menyimpan {$count} data kehadiran.");
    }

    /**
     * Update single student attendance status.
     */
    public function updateSingleAttendance(Request $request)
    {
        $request->validate([
            'nis' => 'required|exists:students,nis',
            'date' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha',
            'kelas_id' => 'required|exists:kelas,id',
            'checktype' => 'nullable|in:masuk,pulang',
        ]);

        $nis = $request->nis;
        $date = $request->date;
        $status = $request->status;
        $kelasId = $request->kelas_id;
        $checktype = $request->checktype ?? 'masuk';

        // Map status to checktype
        $checktypeMap = [
            'sakit' => 2,
            'izin' => 3,
            'alpha' => 4,
        ];

        // Create new record based on status
        if ($status === 'hadir') {
            // Use current time if today, otherwise use default times
            $isToday = Carbon::parse($date)->isToday();
            $checkInTime = $isToday ? now() : Carbon::parse($date)->setTime(7, 0, 0);
            $checkOutTime = $isToday ? now() : Carbon::parse($date)->setTime(16, 0, 0);

            // Delete only the specific record type (masuk or pulang)
            if ($checktype === 'masuk') {
                // Delete existing check-in record
                Attendance::where('nis', $nis)
                    ->whereDate('checktime', $date)
                    ->where('checktype', 0)
                    ->delete();
                // Create check-in record
                Attendance::create([
                    'nis' => $nis,
                    'checktime' => $checkInTime,
                    'checktype' => 0, // Check-in
                ]);
            } elseif ($checktype === 'pulang') {
                // Delete existing check-out record
                Attendance::where('nis', $nis)
                    ->whereDate('checktime', $date)
                    ->where('checktype', 1)
                    ->delete();
                // Create check-out record
                Attendance::create([
                    'nis' => $nis,
                    'checktime' => $checkOutTime,
                    'checktype' => 1, // Check-out
                ]);
            }
        } else {
            // Delete all existing attendance records for this student on this date
            Attendance::where('nis', $nis)
                ->whereDate('checktime', $date)
                ->delete();
            // Create absence record (sakit/izin/alpha)
            $statusChecktype = $checktypeMap[$status];
            Attendance::create([
                'nis' => $nis,
                'checktime' => Carbon::parse($date)->setTime(0, 0, 0),
                'checktype' => $statusChecktype,
            ]);
        }

        return redirect()->route('admin.attendance.showByKelas', ['kelasId' => $kelasId, 'date' => $date])
            ->with('success', "Berhasil mengubah status kehadiran.");
    }

    /**
     * Bulk update multiple students' attendance status.
     */
    public function bulkUpdateAttendance(Request $request)
    {
        $request->validate([
            'students' => 'required|array|min:1',
            'students.*' => 'required|exists:students,nis',
            'date' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha',
            'kelas_id' => 'required|exists:kelas,id',
            'checktype' => 'nullable|in:masuk,pulang',
        ]);

        $students = $request->students;
        $date = $request->date;
        $status = $request->status;
        $kelasId = $request->kelas_id;
        $checktype = $request->checktype ?? 'pulang';

        // Map status to checktype
        $checktypeMap = [
            'sakit' => 2,
            'izin' => 3,
            'alpha' => 4,
        ];

        $count = 0;
        foreach ($students as $nis) {
            // Create new record based on status
            if ($status === 'hadir') {
                // Only update specific checktype (masuk or pulang)
                if ($checktype === 'masuk') {
                    // Delete existing check-in record
                    Attendance::where('nis', $nis)
                        ->whereDate('checktime', $date)
                        ->where('checktype', 0)
                        ->delete();
                    // Create check-in record at 07:00
                    Attendance::create([
                        'nis' => $nis,
                        'checktime' => Carbon::parse($date)->setTime(7, 0, 0),
                        'checktype' => 0, // Check-in
                    ]);
                } elseif ($checktype === 'pulang') {
                    // Delete existing check-out record
                    Attendance::where('nis', $nis)
                        ->whereDate('checktime', $date)
                        ->where('checktype', 1)
                        ->delete();
                    // Create check-out record at 16:00
                    Attendance::create([
                        'nis' => $nis,
                        'checktime' => Carbon::parse($date)->setTime(16, 0, 0),
                        'checktype' => 1, // Check-out
                    ]);
                }
            } else {
                // Delete all existing attendance records for this student on this date
                Attendance::where('nis', $nis)
                    ->whereDate('checktime', $date)
                    ->delete();
                // Create absence record (sakit/izin/alpha)
                $statusChecktype = $checktypeMap[$status];
                Attendance::create([
                    'nis' => $nis,
                    'checktime' => Carbon::parse($date)->setTime(0, 0, 0),
                    'checktype' => $statusChecktype,
                ]);
            }
            $count++;
        }

        return redirect()->route('admin.attendance.showByKelas', ['kelasId' => $kelasId, 'date' => $date])
            ->with('success', "Berhasil mengubah status kehadiran {$count} siswa.");
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

    /**
     * Print bolos students report.
     */
    public function printBolos(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        // Check user role for filtering
        $userRoles = session('user_roles', []);
        $userLevel = session('user_level', '');
        $userId = session('user_id');
        $isWaliKelas = in_array('Wali Kelas', $userRoles);
        $isKepsek = in_array('Kepsek', $userRoles);
        $isAdmin = in_array('Admin', $userRoles) || $isKepsek || $userLevel === 'admin';
        $isPiket = in_array('Piket', $userRoles);

        $walasKelasId = null;
        $shouldFilterByWalasKelas = $isWaliKelas && !$isAdmin && !$isPiket;

        if ($shouldFilterByWalasKelas) {
            $guru = \App\Models\Guru::where('user_id', $userId)->first();
            if ($guru) {
                $walas = Walas::with('kelas')
                    ->where('guru_id', $guru->id)
                    ->where('is_active', true)
                    ->first();
                if ($walas) {
                    $walasKelasId = $walas->kelas_id;
                }
            }
        }

        // Get students query
        $studentsQuery = Student::with('kelas')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereHas('kelas', function ($q) {
                    $q->where('nm_kls', '!=', '-');
                });
            });

        if ($shouldFilterByWalasKelas && $walasKelasId) {
            $studentsQuery->where('kelas_id', $walasKelasId);
        }

        $students = $studentsQuery->orderBy('name')->get();

        // Get all attendance data for the date
        $allAttendanceData = Attendance::whereDate('checktime', $date)->get()->groupBy('nis');

        // Find bolos students (check-in exists but no check-out)
        $bolosStudents = [];

        foreach ($students as $student) {
            $records = $allAttendanceData->get($student->nis);

            if (!$records) {
                continue;
            }

            // Skip if has special status (Sakit/Izin/Alpha)
            $specialRecord = $records->whereIn('checktype', [2, 3, 4])->first();
            if ($specialRecord) {
                continue;
            }

            // Check for normal attendance
            $checkIn = $records->where('checktype', 0)->sortBy('checktime')->first();
            $checkOut = $records->where('checktype', 1)->sortByDesc('checktime')->first();

            // Check if PKL attendance
            $isPklAttendance = $checkIn ? ($checkIn->is_pkl ?? false) : false;

            // Bolos: has check-in but no check-out (for non-PKL)
            if ($checkIn && !$checkOut && !$isPklAttendance) {
                $bolosStudents[] = [
                    'nis' => $student->nis,
                    'name' => $student->name,
                    'kelas' => $student->kelas->nm_kls ?? '-',
                    'jam_masuk' => $checkIn->checktime->format('H:i'),
                ];
            }
        }

        // Sort by kelas and then by name
        usort($bolosStudents, function ($a, $b) {
            $kelasCompare = strcmp($a['kelas'], $b['kelas']);
            if ($kelasCompare === 0) {
                return strcmp($a['name'], $b['name']);
            }
            return $kelasCompare;
        });

        // Get settings for school info
        $schoolName = \App\Models\Setting::get('school_name', 'SMK NEGERI 1');
        $city = \App\Models\Setting::get('city', 'Seputih Agung');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.attendance.print-bolos', compact(
            'bolosStudents',
            'date',
            'schoolName',
            'city'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("siswa_bolos_{$date}.pdf");
    }

    /**
     * Print students who haven't checked in report.
     */
    public function printBelumMasuk(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        // Check user role for filtering
        $userRoles = session('user_roles', []);
        $userLevel = session('user_level', '');
        $userId = session('user_id');
        $isWaliKelas = in_array('Wali Kelas', $userRoles);
        $isKepsek = in_array('Kepsek', $userRoles);
        $isAdmin = in_array('Admin', $userRoles) || $isKepsek || $userLevel === 'admin';
        $isPiket = in_array('Piket', $userRoles);

        $walasKelasId = null;
        $shouldFilterByWalasKelas = $isWaliKelas && !$isAdmin && !$isPiket;

        if ($shouldFilterByWalasKelas) {
            $guru = \App\Models\Guru::where('user_id', $userId)->first();
            if ($guru) {
                $walas = Walas::with('kelas')
                    ->where('guru_id', $guru->id)
                    ->where('is_active', true)
                    ->first();
                if ($walas) {
                    $walasKelasId = $walas->kelas_id;
                }
            }
        }

        // Get students query
        $studentsQuery = Student::with('kelas')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereHas('kelas', function ($q) {
                    $q->where('nm_kls', '!=', '-');
                });
            });

        if ($shouldFilterByWalasKelas && $walasKelasId) {
            $studentsQuery->where('kelas_id', $walasKelasId);
        }

        $students = $studentsQuery->orderBy('name')->get();

        // Get all NIS that have any attendance record for the date
        $attendedNis = Attendance::whereDate('checktime', $date)
            ->pluck('nis')
            ->unique()
            ->toArray();

        // Find students who haven't checked in (no attendance record at all)
        $belumMasukStudents = [];

        foreach ($students as $student) {
            // If student has no attendance record for this date
            if (!in_array($student->nis, $attendedNis)) {
                $belumMasukStudents[] = [
                    'nis' => $student->nis,
                    'name' => $student->name,
                    'kelas' => $student->kelas->nm_kls ?? '-',
                ];
            }
        }

        // Sort by kelas and then by name
        usort($belumMasukStudents, function ($a, $b) {
            $kelasCompare = strcmp($a['kelas'], $b['kelas']);
            if ($kelasCompare === 0) {
                return strcmp($a['name'], $b['name']);
            }
            return $kelasCompare;
        });

        // Get settings for school info
        $schoolName = \App\Models\Setting::get('school_name', 'SMK NEGERI 1');
        $city = \App\Models\Setting::get('city', 'Seputih Agung');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.attendance.print-belum-masuk', compact(
            'belumMasukStudents',
            'date',
            'schoolName',
            'city'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("siswa_belum_masuk_{$date}.pdf");
    }
}
