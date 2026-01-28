<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pkl;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunPelajaran;
use App\Models\MUser;
use App\Models\PklSoftNilai;
use App\Models\PklHardNilai;
use App\Models\PklWirausahaNilai;
use App\Models\PklKompSoft;
use App\Models\PklKompHard;
use App\Models\PklKompWirausaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GuruPklController extends Controller
{
    /**
     * Display PKL list for the logged-in teacher
     */
    public function index(Request $request)
    {
        $userId = Session::get('user_id');
        $userLevel = Session::get('user_level');
        $userRoles = Session::get('user_roles', []);

        // Ensure user has access (either level guru or role PKL)
        if ($userLevel !== 'guru' && !in_array('PKL', $userRoles)) {
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak.');
        }

        // Get the associated guru
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            // If user is admin/other but has role PKL (edge case), or guru data missing
            // Ideally this module is for when the user is linked to a Guru entity
            return redirect()->route('admin.dashboard')->with('error', 'Data guru tidak ditemukan untuk akun ini.');
        }

        // Filter parameters
        $dudiId = $request->get('dudi_id');
        $search = $request->get('search');

        // Get active TP or selected TP
        $activeTp = TahunPelajaran::where('is_active', true)->first();
        $tpId = $request->get('tp_id', $activeTp?->id);

        // Get DUDI list (only those associated with PKL students guided by this teacher)
        $dudiList = \App\Models\Dudi::whereHas('pkls', function ($q) use ($guru) {
            $q->where('pembimbing_sekolah_id', $guru->id);
        })->orderBy('nama')->get();

        // Get TP List
        $tpList = TahunPelajaran::orderBy('nm_tp', 'desc')->get();
        $selectedTp = $tpId ? TahunPelajaran::find($tpId) : $activeTp;

        // Build Query
        // Filter by pembimbing_sekolah_id = guru->id
        $query = Pkl::select('pkls.*')
            ->join('dudis', 'pkls.dudi_id', '=', 'dudis.id')
            ->with(['student.kelas', 'dudi', 'tahunPelajaran'])
            ->where('pkls.pembimbing_sekolah_id', $guru->id)
            ->orderBy('dudis.nama', 'asc')
            ->orderBy('pkls.student_id', 'asc');

        // Apply filters
        if ($tpId) {
            $query->where('pkls.tp_id', $tpId);
        }

        if ($dudiId) {
            $query->where('pkls.dudi_id', $dudiId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                })
                    ->orWhereHas('dudi', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        // Get all data first then group (removing pagination to simplify grouping display)
        $allPkls = $query->get();

        // Group by DUDI Name
        $groupedPkls = $allPkls->groupBy(function ($pkl) {
            return $pkl->dudi->nama;
        });

        // Get Sertifikat configs keyed by TP ID
        $sertifikatsByTp = \App\Models\Sertifikat::all()->keyBy('m_tp_id');

        // Get kelas list for adding students (XI and XII)
        $kelasList = Kelas::where('nm_kls', 'like', '%XI%')
            ->orWhere('nm_kls', 'like', '%XII%')
            ->orderBy('nm_kls')
            ->get();

        // Get all DUDI list (for adding students to any DUDI)
        $allDudiList = \App\Models\Dudi::orderBy('nama')->get();

        return view('admin.guru.pkl.index', compact(
            'groupedPkls',
            'dudiList',
            'allDudiList',
            'kelasList',
            'tpList',
            'selectedTp',
            'tpId',
            'dudiId',
            'search',
            'guru',
            'sertifikatsByTp'
        ));
    }

    public function inputNilai($id)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $pkl = Pkl::with(['student.kelas', 'dudi', 'tahunPelajaran'])->findOrFail($id);

        // Security check: ensure student belongs to this guru
        if ($pkl->pembimbing_sekolah_id !== $guru->id) {
            return redirect()->route('admin.guru.pkl.index')->with('error', 'Siswa ini bukan bimbingan Anda.');
        }

        $jurusanId = $pkl->student->m_jurusan_id;

        // Get Components
        $kompSoft = PklKompSoft::where('m_jurusan_id', $jurusanId)->orderBy('nama')->get();
        $kompHard = PklKompHard::where('m_jurusan_id', $jurusanId)->orderBy('nama')->get();
        $kompWirausaha = PklKompWirausaha::where('m_jurusan_id', $jurusanId)->orderBy('nama')->get();

        // Get Existing Scores
        $softNilai = PklSoftNilai::where('student_id', $pkl->student_id)->pluck('nilai', 'pkl_kompsoft_id');
        $hardNilai = PklHardNilai::where('student_id', $pkl->student_id)->pluck('nilai', 'pkl_komphard_id');
        $wirausahaNilai = PklWirausahaNilai::where('student_id', $pkl->student_id)->pluck('nilai', 'pkl_kompwirausaha_id');

        return view('admin.guru.pkl.input_nilai', compact(
            'pkl',
            'kompSoft',
            'kompHard',
            'kompWirausaha',
            'softNilai',
            'hardNilai',
            'wirausahaNilai'
        ));
    }

    public function storeNilai(Request $request, $id)
    {
        $pkl = Pkl::findOrFail($id);
        $studentId = $pkl->student_id;
        $dudiId = $pkl->dudi_id;
        $tpId = $pkl->tp_id;

        // Save Soft Skills
        if ($request->has('nilai_soft')) {
            foreach ($request->nilai_soft as $kompId => $nilai) {
                if ($nilai !== null) {
                    PklSoftNilai::updateOrCreate(
                        ['student_id' => $studentId, 'pkl_kompsoft_id' => $kompId],
                        ['nilai' => $nilai, 'dudi_id' => $dudiId, 'm_tp_id' => $tpId]
                    );
                }
            }
        }

        // Save Hard Skills
        if ($request->has('nilai_hard')) {
            foreach ($request->nilai_hard as $kompId => $nilai) {
                if ($nilai !== null) {
                    PklHardNilai::updateOrCreate(
                        ['student_id' => $studentId, 'pkl_komphard_id' => $kompId],
                        ['nilai' => $nilai, 'dudi_id' => $dudiId, 'm_tp_id' => $tpId]
                    );
                }
            }
        }

        // Save Wirausaha
        if ($request->has('nilai_wirausaha')) {
            foreach ($request->nilai_wirausaha as $kompId => $nilai) {
                if ($nilai !== null) {
                    PklWirausahaNilai::updateOrCreate(
                        ['student_id' => $studentId, 'pkl_kompwirausaha_id' => $kompId],
                        ['nilai' => $nilai, 'dudi_id' => $dudiId, 'm_tp_id' => $tpId]
                    );
                }
            }
        }

        return redirect()->route('admin.guru.pkl.index')->with('success', 'Nilai berhasil disimpan.');
    }

    /**
     * Display DUDI list for setting location
     */
    public function setLokasi()
    {
        $userId = Session::get('user_id');
        $userLevel = Session::get('user_level');
        $userRoles = Session::get('user_roles', []);

        // Ensure user has access (either level guru or role PKL)
        if ($userLevel !== 'guru' && !in_array('PKL', $userRoles)) {
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak.');
        }

        // Get the associated guru
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->route('admin.dashboard')->with('error', 'Data guru tidak ditemukan untuk akun ini.');
        }

        // Get DUDI list (only those associated with PKL students guided by this teacher)
        $dudiList = \App\Models\Dudi::whereHas('pkls', function ($q) use ($guru) {
            $q->where('pembimbing_sekolah_id', $guru->id);
        })->orderBy('nama')->get();

        return view('admin.guru.pkl.set_lokasi', compact('dudiList', 'guru'));
    }

    /**
     * Update DUDI location
     */
    public function updateLokasi(Request $request, $id)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // Verify this DUDI is associated with this guru
        $dudi = \App\Models\Dudi::whereHas('pkls', function ($q) use ($guru) {
            $q->where('pembimbing_sekolah_id', $guru->id);
        })->findOrFail($id);

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:1000',
        ]);

        $dudi->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
        ]);

        return redirect()->route('admin.guru.pkl.set_lokasi')->with('success', "Lokasi {$dudi->nama} berhasil diperbarui.");
    }

    /**
     * Store new PKL record(s) - supports multiple students
     */
    public function store(Request $request)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'dudi_id' => 'required|exists:dudis,id',
            'tp_id' => 'required|exists:m_tp,id',
        ]);

        $created = 0;
        foreach ($request->student_ids as $studentId) {
            // Check if student already has PKL in this tahun pelajaran
            $existingPkl = Pkl::where('student_id', $studentId)
                ->where('tp_id', $request->tp_id)
                ->first();

            if ($existingPkl) {
                continue; // Skip if student already has PKL in this TP
            }

            Pkl::create([
                'student_id' => $studentId,
                'dudi_id' => $request->dudi_id,
                'pembimbing_sekolah_id' => $guru->id, // This guru becomes the pembimbing
                'tp_id' => $request->tp_id,
                'created_by' => $userId,
            ]);
            $created++;
        }

        if ($created == 0) {
            return redirect()->route('admin.guru.pkl.index')->with('error', 'Semua siswa yang dipilih sudah terdaftar PKL di tahun pelajaran ini');
        }

        return redirect()->route('admin.guru.pkl.index')->with('success', "{$created} data PKL berhasil ditambahkan");
    }

    /**
     * Delete PKL record
     */
    public function destroy($id)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $pkl = Pkl::where('id', $id)
            ->where('pembimbing_sekolah_id', $guru->id) // Only allow deleting own students
            ->firstOrFail();

        $pkl->delete();

        return redirect()->route('admin.guru.pkl.index')->with('success', 'Data PKL berhasil dihapus');
    }

    /**
     * Get students by kelas for AJAX (exclude those with PKL in selected TP)
     */
    public function getStudentsByKelas(Request $request, $kelasId)
    {
        $tpId = $request->get('tp_id');

        $query = Pkl::query();
        if ($tpId) {
            $query->where('tp_id', $tpId);
        }
        $studentsWithPkl = $query->pluck('student_id')->toArray();

        $students = \App\Models\Student::where('kelas_id', $kelasId)
            ->whereNotIn('id', $studentsWithPkl)
            ->orderBy('name')
            ->get(['id', 'nis', 'name']);

        return response()->json($students);
    }

    /**
     * Display PKL attendance for students guided by this teacher
     */
    public function absensi(Request $request)
    {
        $userId = Session::get('user_id');
        $userLevel = Session::get('user_level');
        $userRoles = Session::get('user_roles', []);

        // Ensure user has access (either level guru or role PKL)
        if ($userLevel !== 'guru' && !in_array('PKL', $userRoles)) {
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak.');
        }

        // Get the associated guru
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->route('admin.dashboard')->with('error', 'Data guru tidak ditemukan untuk akun ini.');
        }

        // Filter parameters
        $dudiId = $request->get('dudi_id');
        $date = $request->get('date', now()->format('Y-m-d'));
        $search = $request->get('search');

        // Get active TP or selected TP
        $activeTp = TahunPelajaran::where('is_active', true)->first();
        $tpId = $request->get('tp_id', $activeTp?->id);

        // Get DUDI list (only those associated with PKL students guided by this teacher)
        $dudiList = \App\Models\Dudi::whereHas('pkls', function ($q) use ($guru) {
            $q->where('pembimbing_sekolah_id', $guru->id);
        })->orderBy('nama')->get();

        // Get TP List
        $tpList = TahunPelajaran::orderBy('nm_tp', 'desc')->get();
        $selectedTp = $tpId ? TahunPelajaran::find($tpId) : $activeTp;

        // Get PKL students guided by this teacher
        $pklQuery = Pkl::select('pkls.*')
            ->join('dudis', 'pkls.dudi_id', '=', 'dudis.id')
            ->with(['student.kelas', 'dudi'])
            ->where('pkls.pembimbing_sekolah_id', $guru->id)
            ->orderBy('dudis.nama', 'asc')
            ->orderBy('pkls.student_id', 'asc');

        if ($tpId) {
            $pklQuery->where('pkls.tp_id', $tpId);
        }

        if ($dudiId) {
            $pklQuery->where('pkls.dudi_id', $dudiId);
        }

        if ($search) {
            $pklQuery->where(function ($q) use ($search) {
                $q->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            });
        }

        $pkls = $pklQuery->get();

        // Get NIS list of PKL students
        $nisList = $pkls->pluck('student.nis')->toArray();

        // Get attendance data for selected date (PKL attendance only)
        $attendances = \App\Models\Attendance::whereIn('nis', $nisList)
            ->where('is_pkl', true)
            ->whereDate('checktime', $date)
            ->get()
            ->groupBy('nis');

        // Build attendance data for each student
        $attendanceData = [];
        foreach ($pkls as $pkl) {
            $nis = $pkl->student->nis;
            $studentAttendances = $attendances->get($nis, collect());

            $checkIn = $studentAttendances->where('checktype', 0)->first();
            $checkOut = $studentAttendances->where('checktype', 1)->first();
            $sakit = $studentAttendances->where('checktype', 2)->first();
            $izin = $studentAttendances->where('checktype', 3)->first();
            $alpha = $studentAttendances->where('checktype', 4)->first();

            $attendanceData[$pkl->id] = [
                'pkl' => $pkl,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'status' => $this->getAttendanceStatus($checkIn, $checkOut, $sakit, $izin, $alpha),
            ];
        }

        // Group by DUDI
        $groupedData = collect($attendanceData)->groupBy(function ($item) {
            return $item['pkl']->dudi->nama;
        });

        return view('admin.guru.pkl.absensi', compact(
            'groupedData',
            'dudiList',
            'tpList',
            'selectedTp',
            'tpId',
            'dudiId',
            'date',
            'search',
            'guru'
        ));
    }

    /**
     * Helper to determine attendance status
     */
    private function getAttendanceStatus($checkIn, $checkOut, $sakit = null, $izin = null, $alpha = null): string
    {
        // First check for Sakit/Izin/Alpha
        if ($sakit) {
            return 'Sakit';
        } elseif ($izin) {
            return 'Izin';
        } elseif ($alpha) {
            return 'Alpha';
        } elseif ($checkIn && $checkOut) {
            return 'Lengkap';
        } elseif ($checkIn) {
            return 'Belum Pulang';
        } else {
            return 'Belum Absen';
        }
    }

    /**
     * Update PKL attendance status (Sakit, Izin, Alpha)
     */
    public function updateAbsensiStatus(Request $request)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'pkl_id' => 'required|exists:pkls,id',
            'status' => 'required|in:2,3,4', // 2=Sakit, 3=Izin, 4=Alpha
            'date' => 'required|date',
        ]);

        // Verify this PKL belongs to this guru
        $pkl = Pkl::where('id', $request->pkl_id)
            ->where('pembimbing_sekolah_id', $guru->id)
            ->first();

        if (!$pkl) {
            return redirect()->back()->with('error', 'Siswa ini bukan bimbingan Anda.');
        }

        $nis = $pkl->student->nis;
        $date = $request->date;
        $checktype = (int) $request->status;

        // Check if attendance record already exists for this student on this date
        $existingAttendance = \App\Models\Attendance::where('nis', $nis)
            ->where('is_pkl', true)
            ->whereDate('checktime', $date)
            ->first();

        if ($existingAttendance) {
            // Update existing record
            $existingAttendance->update([
                'checktype' => $checktype,
                'checktime' => $date . ' 00:00:00',
            ]);
        } else {
            // Create new attendance record
            \App\Models\Attendance::create([
                'nis' => $nis,
                'checktime' => $date . ' 00:00:00',
                'checktype' => $checktype,
                'dudi_id' => $pkl->dudi_id,
                'is_pkl' => true,
            ]);
        }

        $statusLabel = match ($checktype) {
            2 => 'Sakit',
            3 => 'Izin',
            4 => 'Alpha',
            default => 'Unknown',
        };

        return redirect()->back()->with('success', "Status absensi {$pkl->student->name} berhasil diubah menjadi {$statusLabel}");
    }

    /**
     * Print PKL attendance report
     */
    public function printAbsensi(Request $request)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->route('admin.guru.pkl.absensi')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'dudi_id' => 'required|exists:dudis,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $dudiId = $request->get('dudi_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $tpId = $request->get('tp_id');

        // Get DUDI info
        $dudi = \App\Models\Dudi::findOrFail($dudiId);

        // Get PKL students for this DUDI guided by this teacher
        $pklQuery = Pkl::with(['student.kelas'])
            ->where('pembimbing_sekolah_id', $guru->id)
            ->where('dudi_id', $dudiId);

        if ($tpId) {
            $pklQuery->where('tp_id', $tpId);
        }

        $pkls = $pklQuery->orderBy('student_id')->get();

        if ($pkls->isEmpty()) {
            return redirect()->route('admin.guru.pkl.absensi')->with('error', 'Tidak ada siswa bimbingan di DUDI ini.');
        }

        // Get NIS list
        $nisList = $pkls->pluck('student.nis')->toArray();

        // Generate date range
        $dates = [];
        $currentDate = \Carbon\Carbon::parse($startDate);
        $endDateCarbon = \Carbon\Carbon::parse($endDate);

        while ($currentDate->lte($endDateCarbon)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // Get all attendance for the date range
        $attendances = \App\Models\Attendance::whereIn('nis', $nisList)
            ->where('is_pkl', true)
            ->whereDate('checktime', '>=', $startDate)
            ->whereDate('checktime', '<=', $endDate)
            ->get()
            ->groupBy([
                'nis',
                function ($item) {
                    return \Carbon\Carbon::parse($item->checktime)->format('Y-m-d');
                }
            ]);

        // Build attendance matrix
        $attendanceMatrix = [];
        foreach ($pkls as $pkl) {
            $nis = $pkl->student->nis;
            $studentData = [
                'pkl' => $pkl,
                'dates' => [],
            ];

            foreach ($dates as $date) {
                $dayAttendances = $attendances->get($nis, collect())->get($date, collect());

                $checkIn = $dayAttendances->where('checktype', 0)->first();
                $checkOut = $dayAttendances->where('checktype', 1)->first();
                $sakit = $dayAttendances->where('checktype', 2)->first();
                $izin = $dayAttendances->where('checktype', 3)->first();
                $alpha = $dayAttendances->where('checktype', 4)->first();

                $studentData['dates'][$date] = [
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $this->getAttendanceStatus($checkIn, $checkOut, $sakit, $izin, $alpha),
                ];
            }

            $attendanceMatrix[] = $studentData;
        }

        // Get TP info
        $selectedTp = $tpId ? TahunPelajaran::find($tpId) : null;

        return view('admin.guru.pkl.print-absensi', compact(
            'dudi',
            'attendanceMatrix',
            'dates',
            'startDate',
            'endDate',
            'selectedTp',
            'guru'
        ));
    }

    /**
     * Display surat izin list for supervised students
     */
    public function suratIzin(Request $request)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->route('admin.dashboard')->with('error', 'Data guru tidak ditemukan.');
        }

        // Get active TP
        $activeTp = TahunPelajaran::where('is_active', true)->first();
        $tpId = $request->get('tp_id', $activeTp?->id);
        $selectedTp = $tpId ? TahunPelajaran::find($tpId) : $activeTp;

        // Get supervised PKL
        $pklQuery = Pkl::where('pembimbing_sekolah_id', $guru->id);
        if ($selectedTp) {
            $pklQuery->where('tp_id', $selectedTp->id);
        }
        $supervisedStudentIds = $pklQuery->pluck('student_id')->toArray();

        // Get DUDI list for filter
        $dudiList = Pkl::where('pembimbing_sekolah_id', $guru->id)
            ->when($selectedTp, fn($q) => $q->where('tp_id', $selectedTp->id))
            ->with('dudi')
            ->get()
            ->pluck('dudi')
            ->unique('id')
            ->values();

        // Filter
        $status = $request->get('status');
        $dudiId = $request->get('dudi_id');
        $search = $request->get('search');

        // Build surat izin query
        $query = \App\Models\SuratIzin::whereIn('student_id', $supervisedStudentIds)
            ->with(['student.kelas', 'pkl.dudi'])
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        if ($dudiId) {
            $pklIds = Pkl::where('dudi_id', $dudiId)
                ->whereIn('student_id', $supervisedStudentIds)
                ->pluck('id');
            $query->whereIn('pkl_id', $pklIds);
        }

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $suratIzins = $query->paginate(15);
        $tahunPelajarans = TahunPelajaran::orderByDesc('is_active')->orderByDesc('id')->get();

        // Summary counts
        $totalPending = \App\Models\SuratIzin::whereIn('student_id', $supervisedStudentIds)->where('status', 'pending')->count();
        $totalDisetujui = \App\Models\SuratIzin::whereIn('student_id', $supervisedStudentIds)->where('status', 'disetujui')->count();
        $totalDitolak = \App\Models\SuratIzin::whereIn('student_id', $supervisedStudentIds)->where('status', 'ditolak')->count();

        return view('admin.guru.pkl.surat-izin', compact(
            'suratIzins',
            'guru',
            'dudiList',
            'tahunPelajarans',
            'selectedTp',
            'totalPending',
            'totalDisetujui',
            'totalDitolak'
        ));
    }

    /**
     * Update status surat izin
     */
    public function updateSuratIzinStatus(Request $request, $id)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_guru' => 'nullable|string|max:500',
        ]);

        // Get supervised student IDs
        $supervisedStudentIds = Pkl::where('pembimbing_sekolah_id', $guru->id)
            ->pluck('student_id')
            ->toArray();

        $suratIzin = \App\Models\SuratIzin::whereIn('student_id', $supervisedStudentIds)
            ->where('id', $id)
            ->where('status', 'pending')
            ->with(['student', 'pkl'])
            ->firstOrFail();

        // Update surat izin status
        $suratIzin->update([
            'status' => $request->status,
            'catatan_guru' => $request->catatan_guru,
            'approved_by' => $guru->id,
            'approved_at' => now(),
        ]);

        // Create/Update attendance records for each day in the izin period
        // Disetujui: Sakit (checktype 2) atau Izin (checktype 3) berdasarkan jenis_izin
        // Ditolak: Alpha (checktype 4)
        if ($request->status === 'disetujui') {
            // Set checktype based on jenis_izin
            $checktype = $suratIzin->jenis_izin === 'sakit' ? 2 : 3;
            $attendanceLabel = $suratIzin->jenis_izin === 'sakit' ? 'Sakit' : 'Izin';
        } else {
            $checktype = 4;
            $attendanceLabel = 'Alpha';
        }
        $nis = $suratIzin->student->nis;
        $dudiId = $suratIzin->pkl->dudi_id ?? null;

        // Loop through each day from tanggal_mulai to tanggal_selesai
        $currentDate = $suratIzin->tanggal_mulai->copy();
        $endDate = $suratIzin->tanggal_selesai->copy();

        while ($currentDate->lte($endDate)) {
            // Check if attendance record already exists for this student on this date
            $existingAttendance = \App\Models\Attendance::where('nis', $nis)
                ->where('is_pkl', true)
                ->whereDate('checktime', $currentDate->format('Y-m-d'))
                ->first();

            if ($existingAttendance) {
                // Update existing record
                $existingAttendance->update([
                    'checktype' => $checktype,
                    'checktime' => $currentDate->format('Y-m-d') . ' 00:00:00',
                ]);
            } else {
                // Create new attendance record
                \App\Models\Attendance::create([
                    'nis' => $nis,
                    'checktime' => $currentDate->format('Y-m-d') . ' 00:00:00',
                    'checktype' => $checktype,
                    'dudi_id' => $dudiId,
                    'is_pkl' => true,
                ]);
            }

            $currentDate->addDay();
        }

        $statusLabel = $request->status === 'disetujui' ? 'disetujui' : 'ditolak';
        return redirect()->back()->with('success', "Surat izin berhasil {$statusLabel}. Status absensi diperbarui menjadi {$attendanceLabel} untuk {$suratIzin->jumlah_hari} hari.");
    }

    /**
     * Preview surat izin file
     */
    public function previewSuratIzin($id)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $supervisedStudentIds = Pkl::where('pembimbing_sekolah_id', $guru->id)
            ->pluck('student_id')
            ->toArray();

        $suratIzin = \App\Models\SuratIzin::whereIn('student_id', $supervisedStudentIds)
            ->where('id', $id)
            ->firstOrFail();

        if (!$suratIzin->file_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($suratIzin->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($suratIzin->file_path);
        $fileName = basename($suratIzin->file_path);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}

