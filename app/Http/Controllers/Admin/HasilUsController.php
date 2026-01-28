<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilUs;
use App\Models\UjianUs;
use App\Models\Student;

use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class HasilUsController extends Controller
{
    public function index(Request $request)
    {
        $query = UjianUs::with(['mapel', 'guru', 'tahunPelajaran', 'hasilUss.student']);

        // Filter by kelas
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        // Filter by tp_id
        $activeTp = TahunPelajaran::where('is_active', true)->first();
        $tpId = $request->filled('tp_id') ? $request->tp_id : ($activeTp ? $activeTp->id : null);
        if ($tpId) {
            $query->where('tp_id', $tpId);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $ujians = $query->where('status', 'selesai')->latest()->paginate(15);
        $tahunPelajarans = TahunPelajaran::orderBy('nm_tp', 'desc')->get();

        return view('admin.soal.hasil-us.index', compact(
            'ujians',
            'tahunPelajarans',
            'activeTp'
        ));
    }

    public function show($ujianId)
    {
        $ujian = UjianUs::with(['mapel', 'guru', 'hasilUss.student'])->findOrFail($ujianId);
        $students = Student::where('tingkat', $ujian->tingkat)->orderBy('nama')->get();

        // Get existing hasil
        $hasilMap = $ujian->hasilUss->keyBy('student_id');

        return view('admin.soal.hasil-us.show', compact('ujian', 'students', 'hasilMap'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ujian_us_id' => 'required|exists:ujian_uss,id',
            'student_id' => 'required|exists:students,id',
            'nilai' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        HasilUs::updateOrCreate(
            [
                'ujian_us_id' => $request->ujian_us_id,
                'student_id' => $request->student_id,
            ],
            [
                'nilai' => $request->nilai,
                'catatan' => $request->catatan,
            ]
        );

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'ujian_us_id' => 'required|exists:ujian_uss,id',
            'hasil' => 'required|array',
            'hasil.*.student_id' => 'required|exists:students,id',
            'hasil.*.nilai' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($request->hasil as $item) {
            if (isset($item['nilai']) && $item['nilai'] !== null) {
                HasilUs::updateOrCreate(
                    [
                        'ujian_us_id' => $request->ujian_us_id,
                        'student_id' => $item['student_id'],
                    ],
                    [
                        'nilai' => $item['nilai'],
                        'catatan' => $item['catatan'] ?? null,
                    ]
                );
            }
        }

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function destroy($id)
    {
        $hasil = HasilUs::findOrFail($id);
        $hasil->delete();

        return back()->with('success', 'Data hasil berhasil dihapus.');
    }

    public function getStudentsByKelas($kelasId)
    {
        $students = Student::where('tingkat', $kelasId)
            ->orderBy('nama')
            ->get(['id', 'nama', 'nisn']);

        return response()->json($students);
    }
}

