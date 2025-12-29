<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pkl;
use App\Models\Dudi;
use App\Models\Student;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunPelajaran;
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
        $search = $request->get('search');

        // Get session TP or use active TP as default
        $activeTp = TahunPelajaran::where('is_active', true)->first();
        $sessionTpId = Session::get('pkl_tp_id', $activeTp?->id);
        $tpId = $request->get('tp_id', $sessionTpId);


        // Get kelas list for filter (all classes)
        $kelasList = Kelas::orderBy('nm_kls')->get();

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
        $query = Pkl::with(['student.kelas', 'dudi', 'pembimbingSekolah', 'tahunPelajaran'])
            ->orderBy('created_at', 'desc');

        if ($kelasId) {
            $query->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
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
                    $dq->where('nama', 'like', "%{$search}%");
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
                'pembimbing_sekolah_id' => $request->pembimbing_sekolah_id,
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
            'tp_id' => 'required|exists:m_tp,id',
        ]);

        $pkl = Pkl::findOrFail($id);
        $pkl->update($request->only([
            'student_id',
            'dudi_id',
            'pembimbing_sekolah_id',
            'tp_id',
        ]));

        return redirect()->route('admin.pkl.index')->with('success', 'Data PKL berhasil diperbarui');
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
}
