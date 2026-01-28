<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Guru;
use App\Models\GuruJurnal;
use App\Models\Kelas;
use App\Models\KelasAjar;
use App\Models\Mapel;
use App\Models\MUser;
use App\Models\Student;
use App\Models\TahunPelajaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;



class GuruJurnalController extends Controller
{
    public function index(Request $request)
    {
        $userId = Session::get('user_id');
        $userLevel = Session::get('user_level');

        // Get filter parameters
        $tpId = $request->get('tp_id');
        $mapelId = $request->get('mapel_id');
        $kelasId = $request->get('kelas_id');
        $semester = $request->get('semester');
        $search = $request->get('search');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Only allow guru level
        if ($userLevel !== 'guru') {
            return redirect()->route('admin.dashboard')->with('error', 'Akses hanya untuk level Guru.');
        }

        // Get the user and find associated guru
        $user = MUser::find($userId);
        $guru = Guru::where('user_id', $userId)->first();

        $tpList = TahunPelajaran::orderBy('nm_tp', 'desc')->get();
        $activeTp = TahunPelajaran::active()->first();

        // Set default tp_id to active TP if not specified
        if (!$tpId && $activeTp) {
            $tpId = $activeTp->id;
        }

        // If no guru linked, show empty page with message
        if (!$guru) {
            $jurnals = collect([]);
            $groupedJurnals = collect([]);
            $mapelList = collect([]);
            $kelasList = collect([]);
            $kelasId = null;
            $usedKelasIds = [];
            return view('admin.guru.jurnal.index', compact('jurnals', 'groupedJurnals', 'kelasList', 'mapelList', 'tpList', 'activeTp', 'tpId', 'mapelId', 'kelasId', 'semester', 'usedKelasIds'))
                ->with('error', 'Akun Anda tidak terhubung dengan data guru. Silakan hubungi administrator.');
        }

        // Get dropdowns data - filter by guru's assigned classes from kelas_ajars
        $guruKelasIds = KelasAjar::where('guru_id', $guru->id)
            ->where('is_active', true)
            ->pluck('kelas_id')
            ->unique();

        // Also include classes from existing journals (in case they were deactivated)
        $jurnalKelasIds = GuruJurnal::where('guru_id', $guru->id)
            ->whereNotNull('kelas_id')
            ->pluck('kelas_id')
            ->unique();

        $allKelasIds = $guruKelasIds->merge($jurnalKelasIds)->unique();
        $kelasList = Kelas::whereIn('id', $allKelasIds)->orderBy('nm_kls')->get();

        // Get mapels based on guru's ajar records
        $mapelIds = \App\Models\GuruAjar::where('guru_id', $guru->id)
            ->where('is_active', true)
            ->pluck('mapel_id');

        // Also include mapels from existing journals (in case they were deactivated)
        $jurnalMapelIds = GuruJurnal::where('guru_id', $guru->id)
            ->whereNotNull('mapel_id')
            ->pluck('mapel_id')
            ->unique();

        $allMapelIds = $mapelIds->merge($jurnalMapelIds)->unique();
        $mapelList = Mapel::whereIn('id', $allMapelIds)->orderBy('nm_mapel')->get();

        // Query jurnals for this guru with filters
        $query = GuruJurnal::with(['kelas', 'mapel', 'tp'])
            ->where('guru_id', $guru->id);

        // Apply TP filter
        if ($tpId) {
            $query->where('tp_id', $tpId);
        }

        // Apply Mapel filter
        if ($mapelId) {
            $query->where('mapel_id', $mapelId);
        }

        // Apply Kelas filter
        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        // Apply Semester filter
        if ($semester) {
            $query->where('semester', $semester);
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('materi', 'like', "%{$search}%")
                    ->orWhere('kegiatan', 'like', "%{$search}%");
            });
        }

        // Order by TMKE (casted to integer) descending so newest meeting is first
        $jurnals = $query->orderByRaw('CAST(tmke AS UNSIGNED) ASC')->get();

        // Group jurnals by mapel, kelas, tp, and semester
        $groupedJurnals = $jurnals->groupBy(function ($jurnal) {
            return $jurnal->mapel_id . '-' . $jurnal->kelas_id . '-' . $jurnal->tp_id . '-' . $jurnal->semester;
        })->map(function ($group) {
            return [
                'mapel' => $group->first()->mapel,
                'kelas' => $group->first()->kelas,
                'tp' => $group->first()->tp,
                'items' => $group
            ];
        });

        // Get used kelas IDs for today (to hide in form)
        $todayJurnals = GuruJurnal::where('guru_id', $guru->id)
            ->whereDate('tanggal', now()->toDateString())
            ->pluck('kelas_id');
        $usedKelasIds = $todayJurnals->unique()->values()->toArray();

        // Get all students grouped by kelas_id
        $allStudents = \App\Models\Student::orderBy('name')->get(['id', 'name', 'kelas_id']);
        $studentsByKelas = $allStudents->groupBy('kelas_id');

        return view('admin.guru.jurnal.index', compact('jurnals', 'groupedJurnals', 'kelasList', 'mapelList', 'tpList', 'activeTp', 'guru', 'usedKelasIds', 'tpId', 'mapelId', 'kelasId', 'semester', 'studentsByKelas'));
    }

    public function store(Request $request)
    {
        $userId = Session::get('user_id');
        $userLevel = Session::get('user_level');

        if (!$userId || $userLevel !== 'guru') {
            return redirect()->route('admin.dashboard');
        }

        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'tp_id' => 'nullable|exists:m_tp,id',
            'semester' => 'nullable|string',
            'kelas_id' => 'nullable|exists:kelas,id',
            'mapel_id' => 'nullable|exists:m_mapels,id',
            'jam_ke' => 'nullable|string',
            'absensi' => 'nullable|string',
            'materi' => 'nullable|string',
            'kegiatan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'tmke' => [
                'nullable',
                'string',
                Rule::unique('guru_jurnals')->where(function ($query) use ($request, $guru) {
                    return $query->where('guru_id', $guru->id)
                        ->where('mapel_id', $request->mapel_id)
                        ->where('kelas_id', $request->kelas_id)
                        ->where('tp_id', $request->tp_id)
                        ->where('semester', $request->semester);
                }),
            ],
        ], [
            'tmke.unique' => 'Pertemuan ke-' . $request->tmke . ' sudah ada untuk kelas dan mapel ini.',
        ]);

        GuruJurnal::create([
            'guru_id' => $guru->id,
            'tp_id' => $request->tp_id,
            'semester' => $request->semester,
            'tanggal' => $request->tanggal,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'jam_ke' => $request->jam_ke,
            'tmke' => $request->tmke,
            'absensi' => $request->absensi,
            'materi' => $request->materi,
            'kegiatan' => $request->kegiatan,
            'catatan' => $request->catatan,
        ]);

        // Update attendance based on student_attendance data
        if ($request->has('student_attendance') && $request->student_attendance) {
            $this->updateStudentAttendance($request->student_attendance, $request->tanggal);
        }

        return redirect()->back()->with('success', 'Jurnal berhasil disimpan.');
    }

    /**
     * Update student attendance records based on journal attendance data
     */
    private function updateStudentAttendance($studentAttendanceJson, $tanggal)
    {
        $attendanceData = json_decode($studentAttendanceJson, true);

        if (!is_array($attendanceData)) {
            return;
        }

        $now = Carbon::now();
        $tanggalDate = Carbon::parse($tanggal);

        foreach ($attendanceData as $studentId => $status) {
            $student = Student::find($studentId);
            if (!$student || !$student->nis) {
                continue;
            }

            $nis = $student->nis;

            // Check existing attendance record for this date
            $existingMasuk = Attendance::where('nis', $nis)
                ->whereDate('checktime', $tanggalDate)
                ->where('checktype', Attendance::TYPE_MASUK)
                ->first();

            switch ($status) {
                case 'H': // Hadir - Create check-in record if not exists
                    if (!$existingMasuk) {
                        Attendance::create([
                            'nis' => $nis,
                            'checktime' => $now,
                            'checktype' => Attendance::TYPE_MASUK,
                            'is_pkl' => false,
                        ]);
                    }
                    break;

                case 'S': // Sakit
                    $this->upsertNonHadirAttendance($nis, $tanggalDate, Attendance::TYPE_SAKIT);
                    break;

                case 'I': // Izin
                    $this->upsertNonHadirAttendance($nis, $tanggalDate, Attendance::TYPE_IZIN);
                    break;

                case 'A': // Alpha
                case 'AL': // Alpha Leave
                    $this->upsertNonHadirAttendance($nis, $tanggalDate, Attendance::TYPE_ALPHA);
                    break;
            }
        }
    }

    /**
     * Insert or update non-hadir (S/I/A) attendance record
     */
    private function upsertNonHadirAttendance($nis, $tanggalDate, $checktype)
    {
        // Find existing S/I/A record for this date
        $existing = Attendance::where('nis', $nis)
            ->whereDate('checktime', $tanggalDate)
            ->whereIn('checktype', [
                Attendance::TYPE_SAKIT,
                Attendance::TYPE_IZIN,
                Attendance::TYPE_ALPHA
            ])
            ->first();

        if ($existing) {
            // Update existing record
            $existing->update(['checktype' => $checktype]);
        } else {
            // Create new record
            Attendance::create([
                'nis' => $nis,
                'checktime' => $tanggalDate->copy()->setTime(now()->hour, now()->minute, now()->second),
                'checktype' => $checktype,
                'is_pkl' => false,
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $userId = Session::get('user_id');
        $userLevel = Session::get('user_level');

        if (!$userId || $userLevel !== 'guru') {
            return redirect()->route('admin.dashboard');
        }

        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $jurnal = GuruJurnal::where('id', $id)->where('guru_id', $guru->id)->firstOrFail();

        $request->validate([
            'tanggal' => 'required|date',
            'tp_id' => 'nullable|exists:m_tp,id',
            'semester' => 'nullable|string',
            'kelas_id' => 'nullable|exists:kelas,id',
            'mapel_id' => 'nullable|exists:m_mapels,id',
            'jam_ke' => 'nullable|string',
            'absensi' => 'nullable|string',
            'materi' => 'nullable|string',
            'kegiatan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'tmke' => [
                'nullable',
                'string',
                Rule::unique('guru_jurnals')->ignore($jurnal->id)->where(function ($query) use ($request, $guru) {
                    return $query->where('guru_id', $guru->id)
                        ->where('mapel_id', $request->mapel_id)
                        ->where('kelas_id', $request->kelas_id)
                        ->where('tp_id', $request->tp_id)
                        ->where('semester', $request->semester);
                }),
            ],
        ], [
            'tmke.unique' => 'Pertemuan ke-' . $request->tmke . ' sudah ada untuk kelas dan mapel ini.',
        ]);

        $jurnal->update([
            'tanggal' => $request->tanggal,
            'tp_id' => $request->tp_id,
            'semester' => $request->semester,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'jam_ke' => $request->jam_ke,
            'tmke' => $request->tmke,
            'absensi' => $request->absensi,
            'materi' => $request->materi,
            'kegiatan' => $request->kegiatan,
            'catatan' => $request->catatan,
        ]);

        // Update attendance based on student_attendance data
        if ($request->has('student_attendance') && $request->student_attendance) {
            $this->updateStudentAttendance($request->student_attendance, $request->tanggal);
        }

        return redirect()->back()->with('success', 'Jurnal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $userId = Session::get('user_id');
        $userLevel = Session::get('user_level');

        if (!$userId || $userLevel !== 'guru') {
            return redirect()->route('admin.dashboard');
        }

        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $jurnal = GuruJurnal::where('id', $id)->where('guru_id', $guru->id)->firstOrFail();
        $jurnal->delete();

        return redirect()->back()->with('success', 'Jurnal berhasil dihapus.');
    }

    public function downloadPdf(Request $request)
    {
        $userId = Session::get('user_id');
        $userLevel = Session::get('user_level');

        if (!$userId || $userLevel !== 'guru') {
            return redirect()->route('admin.dashboard');
        }

        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $mapelId = $request->get('mapel_id');
        $kelasId = $request->get('kelas_id');
        $tpId = $request->get('tp_id');
        $semester = $request->get('semester');

        // Get data
        $mapel = Mapel::find($mapelId);
        $kelas = Kelas::find($kelasId);
        $tp = TahunPelajaran::find($tpId);

        // Query jurnals
        $query = GuruJurnal::with(['kelas', 'mapel', 'tp'])
            ->where('guru_id', $guru->id)
            ->where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId);

        if ($tpId) {
            $query->where('tp_id', $tpId);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        $jurnals = $query->orderBy('tanggal', 'asc')->get();

        // Get settings
        $settings = \App\Models\Setting::first();
        $kopImage = \App\Models\Setting::get('kop_image');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.guru.jurnal.pdf', [
            'jurnals' => $jurnals,
            'guru' => $guru,
            'mapel' => $mapel,
            'kelas' => $kelas,
            'tp' => $tp,
            'semester' => $semester,
            'settings' => $settings,
            'kopImage' => $kopImage,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $mapelName = $mapel ? $mapel->nm_mapel : 'mapel';
        $kelasName = $kelas ? $kelas->nm_kls : 'kelas';
        $filename = 'jurnal_' . $mapelName . '_' . $kelasName . '_' . date('Y-m-d') . '.pdf';
        $filename = str_replace(' ', '_', $filename);

        return $pdf->stream($filename);
    }

    public function getStudentsByKelas($kelas_id)
    {
        $tanggal = request('tanggal', now()->toDateString());

        $students = \App\Models\Student::where('kelas_id', $kelas_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'nis']);

        // Attach attendance status
        $students->transform(function ($student) use ($tanggal) {
            // Get all attendance records for this student on this date
            $attendances = \App\Models\Attendance::where('nis', $student->nis)
                ->whereDate('checktime', $tanggal)
                ->get();

            $status = 'A'; // Default to Alpha if no attendance record

            if ($attendances->count() > 0) {
                // Check for S/I/A records first (prioritize non-hadir status)
                $nonHadirRecord = $attendances->whereIn('checktype', [
                    \App\Models\Attendance::TYPE_SAKIT,
                    \App\Models\Attendance::TYPE_IZIN,
                    \App\Models\Attendance::TYPE_ALPHA,
                ])->first();

                if ($nonHadirRecord) {
                    switch ($nonHadirRecord->checktype) {
                        case \App\Models\Attendance::TYPE_SAKIT:
                            $status = 'S';
                            break;
                        case \App\Models\Attendance::TYPE_IZIN:
                            $status = 'I';
                            break;
                        case \App\Models\Attendance::TYPE_ALPHA:
                            $status = 'A';
                            break;
                    }
                } else {
                    // Check if there's a check-in record (TYPE_MASUK = 0)
                    $hasCheckIn = $attendances->where('checktype', \App\Models\Attendance::TYPE_MASUK)->count() > 0;
                    if ($hasCheckIn) {
                        $status = 'H';
                    }
                }
            }

            $student->initial_status = $status;
            return $student;
        });

        return response()->json($students);
    }
}
