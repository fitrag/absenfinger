<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruNilai;
use App\Models\GuruNilaiDetail;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\MUser;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class GuruNilaiHarianController extends Controller
{
    public function index(Request $request)
    {
        $userId = Session::get('user_id');
        $userLevel = Session::get('user_level');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($userLevel !== 'guru') {
            return redirect()->route('admin.dashboard')->with('error', 'Akses hanya untuk level Guru.');
        }

        $guru = Guru::where('user_id', $userId)->first();

        // Dropdowns
        $kelasList = Kelas::orderBy('nm_kls')->get();
        $tpList = TahunPelajaran::orderBy('nm_tp', 'desc')->get();
        $activeTp = TahunPelajaran::active()->first();

        // Filters
        $tpId = $request->get('tp_id', $activeTp ? $activeTp->id : null);
        $mapelId = $request->get('mapel_id');
        $semester = $request->get('semester');
        // Search removed as judul is gone

        if (!$guru) {
            return view('admin.guru.nilai.index', compact('kelasList', 'tpList', 'activeTp', 'tpId', 'mapelId', 'semester'))
                ->with('error', 'Akun Anda tidak terhubung dengan data guru.');
        }

        $mapelIds = \App\Models\GuruAjar::where('guru_id', $guru->id)
            ->where('is_active', true)
            ->pluck('mapel_id');
        $mapelList = Mapel::whereIn('id', $mapelIds)->orderBy('nm_mapel')->get();

        $query = GuruNilai::with(['kelas', 'mapel', 'tp', 'details.student'])
            ->withAvg('details', 'nilai')
            ->where('guru_id', $guru->id);

        if ($tpId)
            $query->where('tp_id', $tpId);
        if ($mapelId)
            $query->where('mapel_id', $mapelId);
        if ($semester)
            $query->where('semester', $semester);

        $nilais = $query->orderBy('harian_ke', 'asc')->get();

        // Grouping logic remains...
        $grouped = [];
        foreach ($nilais as $nilai) {
            $key = $nilai->mapel_id . '-' . $nilai->kelas_id . '-' . $nilai->tp_id . '-' . $nilai->semester;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'mapel' => $nilai->mapel,
                    'kelas' => $nilai->kelas,
                    'tp' => $nilai->tp,
                    'semester' => $nilai->semester,
                    'items' => []
                ];
            }
            $grouped[$key]['items'][] = $nilai;
        }

        return view('admin.guru.nilai.index', compact('grouped', 'kelasList', 'tpList', 'mapelList', 'activeTp', 'tpId', 'mapelId', 'semester'));
    }

    public function downloadPdfGroup(Request $request)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $mapelId = $request->get('mapel_id');
        $kelasId = $request->get('kelas_id');
        $tpId = $request->get('tp_id');
        $semester = $request->get('semester');

        // Get all nilai entries for this group
        $nilaiEntries = GuruNilai::with(['kelas', 'mapel', 'tp', 'details.student'])
            ->where('guru_id', $guru->id)
            ->where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId)
            ->where('tp_id', $tpId)
            ->where('semester', $semester)
            ->orderBy('harian_ke', 'asc')
            ->get();

        if ($nilaiEntries->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Get related models from first entry (more reliable)
        $firstEntry = $nilaiEntries->first();
        $mapel = $firstEntry->mapel ?? Mapel::find($mapelId);
        $kelas = $firstEntry->kelas ?? Kelas::find($kelasId);
        $tp = $firstEntry->tp ?? TahunPelajaran::find($tpId);

        if (!$mapel || !$kelas || !$tp) {
            return redirect()->back()->with('error', 'Data mapel/kelas/tp tidak ditemukan.');
        }

        // Collect all students from all entries
        $studentsData = [];
        $harianKeList = [];

        foreach ($nilaiEntries as $entry) {
            $harianKeList[] = $entry->harian_ke;
            foreach ($entry->details as $detail) {
                $studentId = $detail->student_id;
                if (!isset($studentsData[$studentId])) {
                    $studentsData[$studentId] = [
                        'student' => $detail->student,
                        'grades' => []
                    ];
                }
                $studentsData[$studentId]['grades'][$entry->harian_ke] = $detail->nilai;
            }
        }

        // Sort students by name
        uasort($studentsData, function ($a, $b) {
            return strcmp($a['student']->name, $b['student']->name);
        });

        // Calculate averages for each student
        foreach ($studentsData as &$data) {
            $grades = array_filter($data['grades'], fn($v) => $v !== null && $v !== '');
            $data['average'] = count($grades) > 0 ? array_sum($grades) / count($grades) : 0;
        }

        // Calculate class average
        $allAverages = array_column($studentsData, 'average');
        $classAverage = count($allAverages) > 0 ? array_sum($allAverages) / count($allAverages) : 0;

        // Get settings for PDF header (same as GuruJurnalController)
        $settings = \App\Models\Setting::first();
        $kopImage = \App\Models\Setting::get('kop_image');

        $pdf = Pdf::loadView('admin.guru.nilai.pdf-group', [
            'guru' => $guru,
            'mapel' => $mapel,
            'kelas' => $kelas,
            'tp' => $tp,
            'semester' => $semester,
            'harianKeList' => $harianKeList,
            'studentsData' => $studentsData,
            'classAverage' => $classAverage,
            'settings' => $settings,
            'kopImage' => $kopImage,
        ]);

        // Setup paper - landscape for many columns
        $pdf->setPaper('a4', count($harianKeList) > 5 ? 'landscape' : 'portrait');

        $mapelName = $mapel ? $mapel->nm_mapel : 'mapel';
        $kelasName = $kelas ? $kelas->nm_kls : 'kelas';
        $filename = 'nilai_harian_' . $mapelName . '_' . $kelasName . '_' . date('Y-m-d') . '.pdf';
        $filename = str_replace(' ', '_', $filename);

        return $pdf->stream($filename);
    }

    public function store(Request $request)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru)
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');

        $request->validate([
            'tp_id' => 'required|exists:m_tp,id',
            'semester' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:m_mapels,id',
            'harian_ke' => 'required|integer|min:1|max:10',
            'students' => 'nullable|array', // Array of student_id => nilai
        ]);

        // Check uniqueness
        $exists = GuruNilai::where('guru_id', $guru->id)
            ->where('tp_id', $request->tp_id)
            ->where('semester', $request->semester)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('harian_ke', $request->harian_ke)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Data Harian ke-' . $request->harian_ke . ' sudah ada untuk kelas dan mapel ini.');
        }

        $nilai = GuruNilai::create([
            'guru_id' => $guru->id,
            'tp_id' => $request->tp_id,
            'semester' => $request->semester,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'harian_ke' => $request->harian_ke,
        ]);

        if ($request->has('students')) {
            foreach ($request->students as $studentId => $score) {
                if ($score !== null && $score !== '') {
                    GuruNilaiDetail::create([
                        'guru_nilai_id' => $nilai->id,
                        'student_id' => $studentId,
                        'nilai' => $score,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();
        $nilaiHeader = GuruNilai::where('id', $id)->where('guru_id', $guru->id)->firstOrFail();

        $request->validate([
            'tp_id' => 'required|exists:m_tp,id',
            'semester' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:m_mapels,id',
            'harian_ke' => 'required|integer|min:1|max:10',
            'students' => 'nullable|array',
        ]);

        // Check uniqueness excluding self
        $exists = GuruNilai::where('guru_id', $guru->id)
            ->where('tp_id', $request->tp_id)
            ->where('semester', $request->semester)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('harian_ke', $request->harian_ke)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Data Harian ke-' . $request->harian_ke . ' sudah ada untuk kelas dan mapel ini.');
        }


        $nilaiHeader->update([
            'tp_id' => $request->tp_id,
            'semester' => $request->semester,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'harian_ke' => $request->harian_ke,
        ]);

        // Sync details
        if ($request->has('students')) {
            foreach ($request->students as $studentId => $score) {
                $detail = GuruNilaiDetail::where('guru_nilai_id', $nilaiHeader->id)
                    ->where('student_id', $studentId)
                    ->first();

                if ($score !== null && $score !== '') {
                    if ($detail) {
                        $detail->update(['nilai' => $score]);
                    } else {
                        GuruNilaiDetail::create([
                            'guru_nilai_id' => $nilaiHeader->id,
                            'student_id' => $studentId,
                            'nilai' => $score,
                        ]);
                    }
                } else {
                    if ($detail) {
                        $detail->delete();
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Nilai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();
        $nilai = GuruNilai::where('id', $id)->where('guru_id', $guru->id)->firstOrFail();
        $nilai->delete();

        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

    public function getStudentsWithGrades(Request $request)
    {
        $kelas_id = $request->kelas_id;
        $nilai_id = $request->nilai_id;

        if (!$kelas_id) {
            return response()->json([]);
        }

        $students = \App\Models\Student::where('kelas_id', $kelas_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'nis']);

        $grades = [];
        if ($nilai_id) {
            $grades = GuruNilaiDetail::where('guru_nilai_id', $nilai_id)
                ->pluck('nilai', 'student_id')
                ->toArray();
        }

        $data = $students->map(function ($s) use ($grades) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'nis' => $s->nis,
                'nilai' => $grades[$s->id] ?? '',
            ];
        });

        return response()->json($data);
    }
}
