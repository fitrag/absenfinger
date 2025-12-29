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
     * Display attendance list.
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

        // Can input/update absence: Admin or Piket (not Wali Kelas unless they are also Admin/Piket)
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

        // Build query for active students
        $query = Student::with(['kelas'])->where('is_active', true);

        // Filter by kelas - force Wali Kelas to their class
        if ($isWaliKelas && !$isAdmin && $walasKelasId) {
            $query->where('kelas_id', $walasKelasId);
        } elseif ($request->has('kelas_id') && $request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Search by NIS or name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('perPage', 36);
        if ($perPage === 'all') {
            $students = $query->orderBy('name')->get();
        } else {
            $students = $query->orderBy('name')->paginate((int) $perPage)->withQueryString();
        }

        // Get attendance data for the date grouped by NIS
        $attendanceData = Attendance::whereDate('checktime', $date)
            ->get()
            ->groupBy('nis')
            ->map(function ($records) {
                // First check for normal check-in (type 0), then check for special status (2,3,4)
                $checkIn = $records->where('checktype', 0)->sortBy('checktime')->first();
                if (!$checkIn) {
                    // Check for Sakit/Izin/Alpha
                    $checkIn = $records->whereIn('checktype', [2, 3, 4])->first();
                }
                return [
                    'check_in' => $checkIn,
                    'check_out' => $records->where('checktype', 1)->sortByDesc('checktime')->first(),
                ];
            });

        // Get kelas list for filter
        if ($isWaliKelas && !$isAdmin && $walasKelasId) {
            $kelasList = Kelas::where('id', $walasKelasId)->get();
        } else {
            $kelasList = Kelas::orderBy('nm_kls')->get();
        }

        // Statistics for selected date - filter by class for Wali Kelas
        if ($isWaliKelas && !$isAdmin && $walasKelasId) {
            $totalStudents = Student::where('kelas_id', $walasKelasId)->where('is_active', true)->count();
            $studentNisForStats = Student::where('kelas_id', $walasKelasId)->where('is_active', true)->pluck('nis')->toArray();
        } else {
            $totalStudents = Student::active()->count();
            $studentNisForStats = null; // null means all students
        }

        // Calculate detailed stats
        $hadirCount = 0;      // Hadir tepat waktu + pulang
        $terlambatCount = 0;  // Terlambat + pulang
        $bolosCount = 0;      // Masuk tapi tidak pulang
        $sakitCount = 0;
        $izinCount = 0;
        $alphaManualCount = 0;

        foreach ($attendanceData as $nis => $data) {
            // Skip if Wali Kelas and student not in their class
            if ($studentNisForStats !== null && !in_array($nis, $studentNisForStats)) {
                continue;
            }

            $checkIn = $data['check_in'];
            $checkOut = $data['check_out'];

            if ($checkIn) {
                // Check for special status (Sakit/Izin/Alpha)
                if ($checkIn->checktype == 2) {
                    $sakitCount++;
                } elseif ($checkIn->checktype == 3) {
                    $izinCount++;
                } elseif ($checkIn->checktype == 4) {
                    $alphaManualCount++;
                } else {
                    // Normal check-in
                    $isLate = $checkIn->checktime->format('H:i') > '07:00';
                    if (!$checkOut) {
                        $bolosCount++;
                    } elseif ($isLate) {
                        $terlambatCount++;
                    } else {
                        $hadirCount++;
                    }
                }
            }
        }

        $presentCount = $hadirCount + $terlambatCount + $bolosCount + $sakitCount + $izinCount + $alphaManualCount;
        $belumAbsenCount = $totalStudents - $presentCount;
        $alphaCount = $alphaManualCount;

        return view('admin.attendance.index', compact(
            'students',
            'attendanceData',
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
    {
        $attendance->load('student');
        return view('admin.attendance.show', compact('attendance'));
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
     * Store absence record (sakit/izin/alpha).
     */
    public function storeAbsence(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'students' => 'required|array|min:1',
            'students.*.nis' => 'required|string|exists:students,nis',
            'students.*.status' => 'nullable|in:sakit,izin,alpha',
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

            $checktype = $checktypeMap[$status];

            // Check if already has an attendance record for this date
            $exists = Attendance::where('nis', $nis)
                ->whereDate('checktime', $date)
                ->exists();

            if (!$exists) {
                Attendance::create([
                    'nis' => $nis,
                    'checktime' => Carbon::parse($date)->setTime(0, 0, 0),
                    'checktype' => $checktype,
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.attendance.index', ['date' => $date])
            ->with('success', "Berhasil menyimpan {$count} data ketidakhadiran.");
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
