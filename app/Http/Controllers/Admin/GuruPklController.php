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

        return view('admin.guru.pkl.index', compact(
            'groupedPkls',
            'dudiList',
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
}
