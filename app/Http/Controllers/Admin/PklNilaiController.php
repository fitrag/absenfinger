<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PklSoftNilai;
use App\Models\PklHardNilai;
use App\Models\PklWirausahaNilai;
use App\Models\PklKompSoft;
use App\Models\PklKompHard;
use App\Models\PklKompWirausaha;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\Pkl;
use Illuminate\Http\Request;

class PklNilaiController extends Controller
{
    /**
     * Display detail page for a student's scores.
     */
    public function show($pklId, $type = 'soft')
    {
        $pkl = Pkl::with(['student.kelas', 'dudi'])->findOrFail($pklId);

        if ($type === 'soft') {
            $nilaiList = PklSoftNilai::where('student_id', $pkl->student_id)
                ->with('komponenSoft')
                ->get();
            $kompList = PklKompSoft::where('m_jurusan_id', $pkl->student->m_jurusan_id)
                ->orderBy('nama')
                ->get();
        } elseif ($type === 'wirausaha') {
            $nilaiList = PklWirausahaNilai::where('student_id', $pkl->student_id)
                ->with('komponenWirausaha')
                ->get();
            $kompList = PklKompWirausaha::where('m_jurusan_id', $pkl->student->m_jurusan_id)
                ->orderBy('nama')
                ->get();
        } else {
            $nilaiList = PklHardNilai::where('student_id', $pkl->student_id)
                ->with('komponenHard')
                ->get();
            $kompList = PklKompHard::where('m_jurusan_id', $pkl->student->m_jurusan_id)
                ->orderBy('nama')
                ->get();
        }

        return view('admin.pkl.nilai.show', compact('pkl', 'nilaiList', 'kompList', 'type'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $dudiId = $request->get('dudi_id');
        $tpId = $request->get('tp_id');
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        // Get active TP or use selected TP
        $tpList = \App\Models\TahunPelajaran::orderBy('nm_tp', 'desc')->get();
        $activeTp = \App\Models\TahunPelajaran::active()->first();

        // If no TP filter selected, use active TP
        if (!$tpId && $activeTp) {
            $tpId = $activeTp->id;
        }

        // Query PKL records (Students) that have assessments
        // Soft Skills
        $softQuery = Pkl::whereHas('softNilai')
            ->with(['student.kelas', 'dudi', 'softNilai.komponenSoft', 'tahunPelajaran'])
            ->join('dudis', 'pkls.dudi_id', '=', 'dudis.id')
            ->select('pkls.*') // Avoid column collision
            ->orderBy('dudis.nama', 'asc')
            ->orderBy('pkls.updated_at', 'desc');

        // Hard Skills
        $hardQuery = Pkl::whereHas('hardNilai')
            ->with(['student.kelas', 'dudi', 'hardNilai.komponenHard', 'tahunPelajaran'])
            ->join('dudis', 'pkls.dudi_id', '=', 'dudis.id')
            ->select('pkls.*')
            ->orderBy('dudis.nama', 'asc')
            ->orderBy('pkls.updated_at', 'desc');

        // Wirausaha
        $wirausahaQuery = Pkl::whereHas('wirausahaNilai')
            ->with(['student.kelas', 'dudi', 'wirausahaNilai.komponenWirausaha', 'tahunPelajaran'])
            ->join('dudis', 'pkls.dudi_id', '=', 'dudis.id')
            ->select('pkls.*')
            ->orderBy('dudis.nama', 'asc')
            ->orderBy('pkls.updated_at', 'desc');

        // Filter by TP
        if ($tpId) {
            $softQuery->where('pkls.tp_id', $tpId);
            $hardQuery->where('pkls.tp_id', $tpId);
            $wirausahaQuery->where('pkls.tp_id', $tpId);
        }

        if ($dudiId) {
            $softQuery->where('pkls.dudi_id', $dudiId);
            $hardQuery->where('pkls.dudi_id', $dudiId);
            $wirausahaQuery->where('pkls.dudi_id', $dudiId);
        }

        if ($kelasId) {
            $softQuery->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
            $hardQuery->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
            $wirausahaQuery->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($search) {
            $searchFn = function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            };
            $softQuery->whereHas('student', $searchFn);
            $hardQuery->whereHas('student', $searchFn);
            $wirausahaQuery->whereHas('student', $searchFn);
        }

        $softNilaiList = $perPage == 'all'
            ? $softQuery->get()
            : $softQuery->paginate((int) $perPage, ['*'], 'soft_page');

        $hardNilaiList = $perPage == 'all'
            ? $hardQuery->get()
            : $hardQuery->paginate((int) $perPage, ['*'], 'hard_page');

        $wirausahaNilaiList = $perPage == 'all'
            ? $wirausahaQuery->get()
            : $wirausahaQuery->paginate((int) $perPage, ['*'], 'wirausaha_page');

        $kelasList = Kelas::orderBy('nm_kls')->get();
        // Students should ideally be those who have PKL, but keeping generic for now or filtering in view?
        // View uses student list for filters/etc if needed, but mainly for the modal.
        $studentList = Student::with('kelas')->orderBy('name')->get();
        // Filter PKL by active TP for modal
        $pklList = Pkl::with(['student.kelas', 'dudi', 'tahunPelajaran'])
            ->when($tpId, fn($q) => $q->where('tp_id', $tpId))
            ->get();
        $dudiList = \App\Models\Dudi::orderBy('nama')->get();
        $kompSoftList = PklKompSoft::with('jurusan')->orderBy('m_jurusan_id')->orderBy('nama')->get();
        $kompHardList = PklKompHard::with('jurusan')->orderBy('m_jurusan_id')->orderBy('nama')->get();
        $kompWirausahaList = PklKompWirausaha::with('jurusan')->orderBy('m_jurusan_id')->orderBy('nama')->get();

        // Get student IDs that already have nilai (to exclude from dropdown)
        $gradedStudentIds = PklSoftNilai::pluck('student_id')
            ->merge(PklHardNilai::pluck('student_id'))
            ->merge(PklWirausahaNilai::pluck('student_id'))
            ->unique()
            ->values()
            ->toArray();

        $stats = [
            'total' => PklSoftNilai::count() + PklHardNilai::count() + PklWirausahaNilai::count(),
            // Calculate combined average if possible, or just separated
            'avgSoft' => PklSoftNilai::avg('nilai') ?? 0,
            'avgHard' => PklHardNilai::avg('nilai') ?? 0,
            'avgWirausaha' => PklWirausahaNilai::avg('nilai') ?? 0,
        ];
        $stats['avgNilai'] = ($stats['avgSoft'] + $stats['avgHard'] + $stats['avgWirausaha']) / 3; // Rough average

        return view('admin.pkl.nilai.index', compact(
            'softNilaiList',
            'hardNilaiList',
            'wirausahaNilaiList',
            'kelasList',
            'studentList',
            'pklList',
            'dudiList',
            'kompSoftList',
            'kompHardList',
            'kompWirausahaList',
            'kelasId',
            'dudiId',
            'tpId',
            'tpList',
            'activeTp',
            'search',
            'perPage',
            'stats',
            'gradedStudentIds'
        ));
    }


    /**
     * Store newly created resources in storage (batch for all komponens).
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ], [
            'student_id.required' => 'Siswa wajib dipilih',
        ]);

        $studentId = $request->student_id;

        // Find active PKL for this student to associate dudi_id and tp_id
        $pkl = Pkl::where('student_id', $studentId)
            ->latest()
            ->first();

        // Use the ID if found, otherwise null (or error?)
        $dudiId = $pkl ? $pkl->dudi_id : null;
        $tpId = $pkl ? $pkl->tp_id : null;

        // Update pimpinan and pembimbing_industri in pkls table
        if ($pkl && ($request->filled('pimpinan') || $request->filled('pembimbing_industri'))) {
            $pkl->update([
                'pimpinan' => $request->pimpinan,
                'pembimbing_industri' => $request->pembimbing_industri,
            ]);
        }

        // Store soft skill nilai
        if ($request->has('nilai_soft') && is_array($request->nilai_soft)) {
            foreach ($request->nilai_soft as $kompId => $nilai) {
                if ($nilai !== null && $nilai !== '') {
                    PklSoftNilai::updateOrCreate(
                        ['student_id' => $studentId, 'pkl_kompsoft_id' => $kompId],
                        [
                            'nilai' => $nilai,
                            'dudi_id' => $dudiId,
                            'm_tp_id' => $tpId
                        ]
                    );
                }
            }
        }

        // Store hard skill nilai
        if ($request->has('nilai_hard') && is_array($request->nilai_hard)) {
            foreach ($request->nilai_hard as $kompId => $nilai) {
                if ($nilai !== null && $nilai !== '') {
                    PklHardNilai::updateOrCreate(
                        ['student_id' => $studentId, 'pkl_komphard_id' => $kompId],
                        [
                            'nilai' => $nilai,
                            'dudi_id' => $dudiId,
                            'm_tp_id' => $tpId
                        ]
                    );
                }
            }
        }

        // Store wirausaha nilai
        if ($request->has('nilai_wirausaha') && is_array($request->nilai_wirausaha)) {
            foreach ($request->nilai_wirausaha as $kompId => $nilai) {
                if ($nilai !== null && $nilai !== '') {
                    PklWirausahaNilai::updateOrCreate(
                        ['student_id' => $studentId, 'pkl_kompwirausaha_id' => $kompId],
                        [
                            'nilai' => $nilai,
                            'dudi_id' => $dudiId,
                            'm_tp_id' => $tpId
                        ]
                    );
                }
            }
        }

        return redirect()->route('admin.pkl.nilai.index')
            ->with('success', 'Nilai PKL berhasil disimpan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Type must be provided to know which table to update
        $type = $request->input('type', 'soft');

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'pkl_kompsoft_id' => 'nullable|exists:pkl_kompsofts,id',
            'pkl_komphard_id' => 'nullable|exists:pkl_komphards,id',
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        // Find active PKL
        $pkl = Pkl::where('student_id', $request->student_id)
            ->latest()
            ->first();
        $dudiId = $pkl ? $pkl->dudi_id : null;

        if ($type === 'soft') {
            $nilai = PklSoftNilai::findOrFail($id);
            $nilai->update([
                'student_id' => $request->student_id,
                'pkl_kompsoft_id' => $request->pkl_kompsoft_id,
                'nilai' => $request->nilai,
                'dudi_id' => $dudiId
            ]);
        } else {
            $nilai = PklHardNilai::findOrFail($id);
            $nilai->update([
                'student_id' => $request->student_id,
                'pkl_komphard_id' => $request->pkl_komphard_id,
                'nilai' => $request->nilai,
                'dudi_id' => $dudiId
            ]);
        }

        return redirect()->route('admin.pkl.nilai.index')
            ->with('success', 'Nilai PKL berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        // Using Request injection to get query param 'type'
        // If destroy generic route doesn't inject Request, we can use helper request()
        $type = request('type', 'soft');

        if ($type === 'soft') {
            $nilai = PklSoftNilai::findOrFail($id);
        } else {
            $nilai = PklHardNilai::findOrFail($id);
        }

        $nilai->delete();

        return redirect()->route('admin.pkl.nilai.index')
            ->with('success', 'Nilai PKL berhasil dihapus');
    }

    /**
     * Bulk update scores from detail modal.
     */
    public function bulkUpdate(Request $request)
    {
        $type = $request->input('type', 'soft');
        $scores = $request->input('scores', []);

        foreach ($scores as $id => $nilai) {
            if ($nilai !== null) {
                if ($type === 'soft') {
                    PklSoftNilai::where('id', $id)->update(['nilai' => $nilai]);
                } elseif ($type === 'wirausaha') {
                    PklWirausahaNilai::where('id', $id)->update(['nilai' => $nilai]);
                } else {
                    PklHardNilai::where('id', $id)->update(['nilai' => $nilai]);
                }
            }
        }

        return redirect()->back()->with('success', 'Nilai berhasil diperbarui');
    }

    /**
     * Get students by kelas for AJAX.
     */
    public function getStudentsByKelas($kelasId)
    {
        $students = Student::where('kelas_id', $kelasId)->orderBy('name')->get();
        return response()->json($students);
    }

    /**
     * Get komponen by jurusan for AJAX.
     */
    public function getKomponenByJurusan($jurusanId)
    {
        $kompSoft = PklKompSoft::where('m_jurusan_id', $jurusanId)->orderBy('nama')->get();
        $kompHard = PklKompHard::where('m_jurusan_id', $jurusanId)->orderBy('nama')->get();

        return response()->json([
            'soft' => $kompSoft,
            'hard' => $kompHard,
        ]);
    }
}
