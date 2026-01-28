<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PdsKonseling;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class KonselingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get active TP
        $tpAktif = TahunPelajaran::where('is_active', true)->first();

        $query = PdsKonseling::with(['student.kelas']);

        // Default: filter by active TP
        if ($tpAktif) {
            $query->where('tp_id', $tpAktif->id);
        }

        // Filter by date
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Search by student name or NIS
        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        $konseling = $query->join('students', 'pds_konselings.student_id', '=', 'students.id')
            ->orderBy('students.name', 'asc')
            ->orderBy('pds_konselings.tanggal', 'desc')
            ->orderBy('pds_konselings.created_at', 'desc')
            ->select('pds_konselings.*')
            ->paginate(15)
            ->withQueryString();

        $kelasList = Kelas::orderBy('nm_kls')->get();
        // Get students who have pelanggaran records for the counseling dropdown
        $students = Student::with(['kelas', 'pelanggarans'])
            ->where('is_active', true)
            ->whereHas('pelanggarans') // Only students with violation records
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                // Get list of violations as array
                $pelanggaranList = $student->pelanggarans->map(function ($p) {
                    return [
                        'jenis' => $p->jenis_pelanggaran,
                        'poin' => $p->poin,
                        'tanggal' => $p->tanggal->format('d/m/Y'),
                    ];
                })->toArray();
                return [
                    'id' => $student->id,
                    'text' => $student->nis . ' - ' . $student->name . ' (' . ($student->kelas->nm_kls ?? '-') . ')',
                    'kelas_id' => $student->kelas_id,
                    'pelanggaranList' => $pelanggaranList,
                    'total_poin' => $student->pelanggarans->sum('poin'),
                ];
            });

        return view('admin.kesiswaan.konseling.index', compact('konseling', 'kelasList', 'students', 'tpAktif'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'tanggal' => 'required|date',
            'permasalahan' => 'required|string',
            'penanganan' => 'nullable|string',
            'hasil' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ttd_siswa' => 'nullable|string',
            'status' => 'required|in:pending,diproses,selesai',
            'tp_id' => 'nullable|exists:m_tp,id',
            'semester' => 'nullable|in:Ganjil,Genap',
        ], [
            'student_id.required' => 'Siswa wajib dipilih',
            'tanggal.required' => 'Tanggal wajib diisi',
            'permasalahan.required' => 'Permasalahan wajib diisi',
            'foto_bukti.image' => 'File harus berupa gambar',
            'foto_bukti.max' => 'Ukuran file maksimal 2MB',
        ]);

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoPath = $request->file('foto_bukti')->store('konseling/foto', 'public');
        }

        PdsKonseling::create([
            'student_id' => $request->student_id,
            'tanggal' => $request->tanggal,
            'permasalahan' => $request->permasalahan,
            'penanganan' => $request->penanganan,
            'hasil' => $request->hasil,
            'keterangan' => $request->keterangan,
            'foto_bukti' => $fotoPath,
            'ttd_siswa' => $request->ttd_siswa,
            'status' => $request->status,
            'tp_id' => $request->tp_id,
            'semester' => $request->semester,
            'created_by' => Session::get('user_id'),
        ]);

        return redirect()->route('admin.kesiswaan.konseling.index')
            ->with('success', 'Data konseling berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PdsKonseling $konseling)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'tanggal' => 'required|date',
            'permasalahan' => 'required|string',
            'penanganan' => 'nullable|string',
            'hasil' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,diproses,selesai',
        ]);

        $konseling->update([
            'student_id' => $request->student_id,
            'tanggal' => $request->tanggal,
            'permasalahan' => $request->permasalahan,
            'penanganan' => $request->penanganan,
            'hasil' => $request->hasil,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.kesiswaan.konseling.index')
            ->with('success', 'Data konseling berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PdsKonseling $konseling)
    {
        $konseling->delete();

        return redirect()->route('admin.kesiswaan.konseling.index')
            ->with('success', 'Data konseling berhasil dihapus');
    }

    /**
     * Get students list for AJAX if needed (searchable dropdown)
     */
    public function getStudents(Request $request)
    {
        $search = $request->search;
        $kelasId = $request->kelas_id;

        $query = Student::with('kelas')->where('is_active', true);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->limit(20)->get()->map(function ($student) {
            return [
                'id' => $student->id,
                'text' => $student->nis . ' - ' . $student->name . ' (' . ($student->kelas->nm_kls ?? '-') . ')'
            ];
        });

        return response()->json($students);
    }

    /**
     * Print konseling by student.
     */
    public function printByStudent(Student $student)
    {
        $konselings = PdsKonseling::with('creator')
            ->where('student_id', $student->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        $settings = \App\Models\Setting::getAllSettings();

        return view('admin.kesiswaan.konseling.print', compact('student', 'konselings', 'settings'));
    }
}
