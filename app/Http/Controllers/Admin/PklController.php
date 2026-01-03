<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pkl;
use App\Models\Dudi;
use App\Models\Student;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunPelajaran;
use App\Models\Role;
use App\Models\MUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PklController extends Controller
{
    /**
     * Display PKL list
     */
    public function index(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $dudiId = $request->get('dudi_id');
        $pembimbingId = $request->get('pembimbing_id');
        $search = $request->get('search');

        // Get session TP or use active TP as default
        $activeTp = TahunPelajaran::where('is_active', true)->first();
        $sessionTpId = Session::get('pkl_tp_id', $activeTp?->id);
        $tpId = $request->get('tp_id', $sessionTpId);

        // Get kelas list for filter (exclude X, only XI and XII)
        $kelasList = Kelas::where('nm_kls', 'like', '%XI%')
            ->orWhere('nm_kls', 'like', '%XII%')
            ->orderBy('nm_kls')
            ->get();

        // Get kelas list for PKL input (only XI and XII)
        $kelasPklList = Kelas::where('nm_kls', 'like', '%XI%')
            ->orWhere('nm_kls', 'like', '%XII%')
            ->orderBy('nm_kls')
            ->get();

        // Get guru list for pembimbing sekolah
        $guruList = Guru::orderBy('nama')->get();

        // Get dudi list
        $dudiList = Dudi::orderBy('nama')->get();

        // Get tahun pelajaran list
        $tpList = TahunPelajaran::orderBy('nm_tp', 'desc')->get();

        // Get selected TP
        $selectedTp = $tpId ? TahunPelajaran::find($tpId) : $activeTp;

        // Build query
        $query = Pkl::select('pkls.*')
            ->join('dudis', 'pkls.dudi_id', '=', 'dudis.id')
            ->with(['student.kelas', 'dudi', 'pembimbingSekolah', 'tahunPelajaran'])
            ->orderBy('dudis.nama', 'asc')
            ->orderBy('pkls.created_at', 'desc');

        if ($kelasId) {
            $query->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($dudiId) {
            $query->where('dudi_id', $dudiId);
        }

        if ($pembimbingId) {
            $query->where('pembimbing_sekolah_id', $pembimbingId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                })
                    ->orWhereHas('dudi', function ($dq) use ($search) {
                        $dq->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($dudiId) {
            $query->where('dudi_id', $dudiId);
        }

        // Always filter by TP if selected
        if ($tpId) {
            $query->where('tp_id', $tpId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('dudi', function ($dq) use ($search) {
                    $dq->where('dudis.nama', 'like', "%{$search}%");
                })
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            });
        }

        $pkls = $query->paginate(15)->withQueryString();

        // Statistics for selected TP
        $stats = [
            'total' => $tpId ? Pkl::where('tp_id', $tpId)->count() : Pkl::count(),
        ];

        return view('admin.pkl.index', compact(
            'pkls',
            'kelasList',
            'kelasPklList',
            'guruList',
            'dudiList',
            'tpList',
            'selectedTp',
            'kelasId',
            'dudiId',
            'tpId',
            'pembimbingId',
            'search',
            'stats'
        ));
    }

    /**
     * Set active tahun pelajaran in session
     */
    public function setTp(Request $request)
    {
        $request->validate([
            'tp_id' => 'required|exists:m_tp,id',
        ]);

        Session::put('pkl_tp_id', $request->tp_id);

        return redirect()->route('admin.pkl.index')->with('success', 'Tahun pelajaran berhasil diubah');
    }

    /**
     * Store new PKL record(s) - supports multiple students
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'dudi_id' => 'required|exists:dudis,id',
            'pembimbing_sekolah_id' => 'nullable|exists:m_gurus,id',
            'pembimbing_industri' => 'nullable|string|max:255',
            'pimpinan' => 'nullable|string|max:255',
            'tp_id' => 'required|exists:m_tp,id',
        ]);

        // Assign PKL Role to Pembimbing Sekolah if not present
        if ($request->pembimbing_sekolah_id) {
            $guru = Guru::find($request->pembimbing_sekolah_id);
            if ($guru && $guru->user_id) {
                $this->assignPklRole($guru->user_id);
            }
        }

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
                'pembimbing_sekolah_id' => $request->pembimbing_sekolah_id,
                'pembimbing_industri' => $request->pembimbing_industri,
                'pimpinan' => $request->pimpinan,
                'tp_id' => $request->tp_id,
                'created_by' => Session::get('user_id'),
            ]);
            $created++;
        }

        if ($created == 0) {
            return redirect()->route('admin.pkl.index')->with('error', 'Semua siswa yang dipilih sudah terdaftar PKL di tahun pelajaran ini');
        }

        return redirect()->route('admin.pkl.index')->with('success', "{$created} data PKL berhasil ditambahkan");
    }

    /**
     * Update PKL record
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'dudi_id' => 'required|exists:dudis,id',
            'pembimbing_sekolah_id' => 'nullable|exists:m_gurus,id',
            'pembimbing_industri' => 'nullable|string|max:255',
            'pimpinan' => 'nullable|string|max:255',
            'tp_id' => 'required|exists:m_tp,id',
        ]);

        // Assign PKL Role to Pembimbing Sekolah if not present
        if ($request->pembimbing_sekolah_id) {
            $guru = Guru::find($request->pembimbing_sekolah_id);
            if ($guru && $guru->user_id) {
                $this->assignPklRole($guru->user_id);
            }
        }

        $pkl = Pkl::findOrFail($id);
        $pkl->update($request->only([
            'student_id',
            'dudi_id',
            'pembimbing_sekolah_id',
            'pembimbing_industri',
            'pimpinan',
            'tp_id',
        ]));

        return redirect()->route('admin.pkl.index')->with('success', 'Data PKL berhasil diperbarui');
    }

    /**
     * Helper to assign PKL role to user if not exists
     */
    private function assignPklRole($userId)
    {
        $pklRole = Role::where('nama_role', 'PKL')->first();

        // Setup Role PKL if it doesn't exist (safety check)
        if (!$pklRole) {
            $pklRole = Role::create([
                'nama_role' => 'PKL',
                'keterangan' => 'Role untuk Pembimbing PKL',
                'is_active' => true
            ]);
        }

        $user = MUser::find($userId);
        if ($user) {
            // Check if user already has the role
            if (!$user->roles()->where('role_id', $pklRole->id)->exists()) {
                $user->roles()->attach($pklRole->id);
            }
        }
    }

    /**
     * Delete PKL record
     */
    public function destroy($id)
    {
        $pkl = Pkl::findOrFail($id);
        $pkl->delete();

        return redirect()->route('admin.pkl.index')->with('success', 'Data PKL berhasil dihapus');
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

        $students = Student::where('kelas_id', $kelasId)
            ->whereNotIn('id', $studentsWithPkl)
            ->orderBy('name')
            ->get(['id', 'nis', 'name']);

        return response()->json($students);
    }

    /**
     * Export PKL data to PDF
     */
    public function exportPdf(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $dudiId = $request->get('dudi_id');
        $pembimbingId = $request->get('pembimbing_id');
        $search = $request->get('search');

        // Get active TP
        $activeTp = TahunPelajaran::where('is_active', true)->first();
        $sessionTpId = Session::get('pkl_tp_id', $activeTp?->id);
        $tpId = $request->get('tp_id', $sessionTpId);

        // Build query
        $query = Pkl::select('pkls.*')
            ->join('dudis', 'pkls.dudi_id', '=', 'dudis.id')
            ->with(['student.kelas', 'dudi', 'pembimbingSekolah', 'tahunPelajaran'])
            ->orderBy('dudis.nama', 'asc')
            ->orderBy('pkls.created_at', 'desc');

        if ($kelasId) {
            $query->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($dudiId) {
            $query->where('dudi_id', $dudiId);
        }

        if ($pembimbingId) {
            $query->where('pembimbing_sekolah_id', $pembimbingId);
        }

        if ($tpId) {
            $query->where('tp_id', $tpId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                })
                    ->orWhereHas('dudi', function ($dq) use ($search) {
                        $dq->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $pkls = $query->get();
        $tp = TahunPelajaran::find($tpId);
        $setting = \App\Models\Setting::first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pkl.print', compact('pkls', 'tp', 'setting'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Data_PKL_' . now()->format('Y-m-d_H-i') . '.pdf');
    }

    /**
     * Export PKL data to Excel
     */
    public function exportExcel(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $dudiId = $request->get('dudi_id');
        $pembimbingId = $request->get('pembimbing_id');
        $search = $request->get('search');

        // Get active TP
        $activeTp = TahunPelajaran::where('is_active', true)->first();
        $sessionTpId = Session::get('pkl_tp_id', $activeTp?->id);
        $tpId = $request->get('tp_id', $sessionTpId);

        // Build query
        $query = Pkl::select('pkls.*')
            ->join('dudis', 'pkls.dudi_id', '=', 'dudis.id')
            ->with(['student.kelas', 'dudi', 'pembimbingSekolah', 'tahunPelajaran'])
            ->orderBy('dudis.nama', 'asc')
            ->orderBy('pkls.created_at', 'desc');

        if ($kelasId) {
            $query->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($dudiId) {
            $query->where('dudi_id', $dudiId);
        }

        if ($pembimbingId) {
            $query->where('pembimbing_sekolah_id', $pembimbingId);
        }

        if ($tpId) {
            $query->where('tp_id', $tpId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                })
                    ->orWhereHas('dudi', function ($dq) use ($search) {
                        $dq->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $pkls = $query->get();
        $tp = TahunPelajaran::find($tpId);

        $data = [
            ['Data Praktik Kerja Lapangan (PKL)'],
            ['Tahun Pelajaran', $tp->nm_tp ?? '-'],
            [],
        ];

        $currentDudiId = null;
        $groupCounter = 0;

        foreach ($pkls as $pkl) {
            // Add group header when DUDI changes
            if ($currentDudiId !== $pkl->dudi_id) {
                $currentDudiId = $pkl->dudi_id;
                $groupCounter = 0;

                // Add empty row before new group (except first)
                if (count($data) > 3) {
                    $data[] = [];
                }

                // Group header with DUDI and Pembimbing
                $dudiInfo = $pkl->dudi->nama ?? '-';
                if ($pkl->pimpinan || $pkl->pembimbing_industri) {
                    $pimpinanInfo = $pkl->pimpinan ? 'Pimpinan: ' . $pkl->pimpinan : '';
                    $pembimbingInfo = $pkl->pembimbing_industri ? 'Pembimbing Industri: ' . $pkl->pembimbing_industri : '';
                    $separator = ($pkl->pimpinan && $pkl->pembimbing_industri) ? ', ' : '';
                    $dudiInfo .= ' (' . $pimpinanInfo . $separator . $pembimbingInfo . ')';
                }
                $data[] = ['Tempat PKL: ' . $dudiInfo, '', '', 'Pembimbing Sekolah: ' . ($pkl->pembimbingSekolah->nama ?? '-')];
                $data[] = ['Alamat: ' . ($pkl->dudi->alamat ?? '-')];
                $data[] = ['No', 'NIS', 'Nama Siswa', 'Kelas'];
            }

            $groupCounter++;
            $data[] = [
                $groupCounter,
                $pkl->student->nis ?? '-',
                $pkl->student->name ?? '-',
                $pkl->student->kelas->nm_kls ?? '-',
            ];
        }

        $filename = 'Data_PKL_' . now()->format('Y-m-d_H-i') . '.xlsx';

        return \Shuchkin\SimpleXLSXGen::fromArray($data)->downloadAs($filename);
    }

    /**
     * Display Suket PKL list
     */
    public function suket(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $dudiId = $request->get('dudi_id');
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        // Get session TP or use active TP as default
        $activeTp = TahunPelajaran::where('is_active', true)->first();
        $sessionTpId = Session::get('pkl_tp_id', $activeTp?->id);
        $tpId = $request->get('tp_id', $sessionTpId);

        // Get kelas list for filter
        $kelasList = Kelas::where('nm_kls', 'like', '%XI%')
            ->orWhere('nm_kls', 'like', '%XII%')
            ->orderBy('nm_kls')
            ->get();

        // Get dudi list
        $dudiList = Dudi::orderBy('nama')->get();

        // Get tahun pelajaran list
        $tpList = TahunPelajaran::orderBy('nm_tp', 'desc')->get();

        // Get selected TP
        $selectedTp = $tpId ? TahunPelajaran::find($tpId) : $activeTp;

        // Build query - only get PKL that have nilai (completed assessment)
        $query = Pkl::select('pkls.*')
            ->join('dudis', 'pkls.dudi_id', '=', 'dudis.id')
            ->with(['student.kelas', 'student.jurusan', 'dudi', 'pembimbingSekolah', 'tahunPelajaran', 'softNilai', 'hardNilai', 'wirausahaNilai'])
            ->whereHas('softNilai')
            ->orderBy('dudis.nama', 'asc')
            ->orderBy('pkls.updated_at', 'desc');

        // Filter by tahun pelajaran
        if ($tpId) {
            $query->where('pkls.tp_id', $tpId);
        }

        if ($kelasId) {
            $query->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($dudiId) {
            $query->where('pkls.dudi_id', $dudiId);
        }

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $pklList = $perPage == 'all'
            ? $query->get()
            : $query->paginate((int) $perPage);

        $stats = [
            'total' => $pklList instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pklList->total() : $pklList->count(),
        ];

        // Get backgrounds from Sertifikat table
        $sertifikat = \App\Models\Sertifikat::first();
        $bgFront = $sertifikat ? $sertifikat->bgFront : null;
        $bgBack = $sertifikat ? $sertifikat->bgBack : null;

        return view('admin.pkl.suket.index', compact(
            'pklList',
            'kelasList',
            'dudiList',
            'tpList',
            'selectedTp',
            'kelasId',
            'dudiId',
            'tpId',
            'search',
            'perPage',
            'stats',
            'bgFront',
            'bgBack',
            'sertifikat'
        ));
    }

    /**
     * Print Suket PKL
     */
    public function printSuket(Request $request, $id)
    {
        $pkl = Pkl::with([
            'student.kelas',
            'student.jurusan',
            'dudi',
            'pembimbingSekolah',
            'tahunPelajaran',
            'softNilai.komponenSoft',
            'hardNilai.komponenHard',
            'wirausahaNilai.komponenWirausaha'
        ])->findOrFail($id);

        // Get settings for school info
        $setting = \App\Models\Setting::first();

        // Calculate averages
        $avgSoft = $pkl->softNilai->avg('nilai') ?? 0;
        $avgHard = $pkl->hardNilai->avg('nilai') ?? 0;
        $avgWirausaha = $pkl->wirausahaNilai->avg('nilai') ?? 0;

        // Calculate final grade: (Soft*40%) + (Hard*50%) + (Wirausaha*10%)
        $finalGrade = ($avgSoft * 0.40) + ($avgHard * 0.50) + ($avgWirausaha * 0.10);

        // Determine predikat
        if ($finalGrade >= 90) {
            $predikat = 'Amat Baik';
            $huruf = 'A';
        } elseif ($finalGrade >= 80) {
            $predikat = 'Baik';
            $huruf = 'B';
        } elseif ($finalGrade >= 70) {
            $predikat = 'Cukup';
            $huruf = 'C';
        } elseif ($finalGrade >= 60) {
            $predikat = 'Kurang';
            $huruf = 'D';
        } else {
            $predikat = 'Sangat Kurang';
            $huruf = 'E';
        }

        // Get backgrounds from Sertifikat table
        $sertifikat = \App\Models\Sertifikat::first();
        $bgFront = $sertifikat ? $sertifikat->bgFront : null;
        $bgBack = $sertifikat ? $sertifikat->bgBack : null;

        // Get paper size from request (default: legal)
        $paperSize = $request->get('paper_size', 'legal');

        return view('admin.pkl.suket.print', compact(
            'pkl',
            'setting',
            'avgSoft',
            'avgHard',
            'avgWirausaha',
            'finalGrade',
            'predikat',
            'huruf',
            'bgFront',
            'bgBack',
            'sertifikat',
            'paperSize'
        ));
    }
    /**
     * Save Suket PKL configuration (backgrounds and dates)
     */
    public function saveSuketConfig(Request $request)
    {
        $request->validate([
            'suket_bg_front' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'suket_bg_back' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tgl_mulai' => 'nullable|date',
            'tgl_selesai' => 'nullable|date',
            'tgl_cetak' => 'nullable|date',
            'm_tp_id' => 'nullable|exists:m_tp,id',
        ]);

        $sertifikat = \App\Models\Sertifikat::first();
        if (!$sertifikat) {
            $sertifikat = new \App\Models\Sertifikat();
        }

        if ($request->hasFile('suket_bg_front')) {
            $file = $request->file('suket_bg_front');
            $filename = 'suket_bg_front_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('settings', $filename, 'public');
            $sertifikat->bgFront = 'settings/' . $filename;
        }

        if ($request->hasFile('suket_bg_back')) {
            $file = $request->file('suket_bg_back');
            $filename = 'suket_bg_back_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('settings', $filename, 'public');
            $sertifikat->bgBack = 'settings/' . $filename;
        }

        // Save dates and tahun pelajaran
        $sertifikat->tgl_mulai = $request->input('tgl_mulai');
        $sertifikat->tgl_selesai = $request->input('tgl_selesai');
        $sertifikat->tgl_cetak = $request->input('tgl_cetak');
        $sertifikat->m_tp_id = $request->input('m_tp_id');
        $sertifikat->save();

        return redirect()->back()->with('success', 'Konfigurasi sertifikat berhasil disimpan');
    }
}
