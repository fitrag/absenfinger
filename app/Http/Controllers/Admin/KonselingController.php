<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PdsKonseling;
use App\Models\Student;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class KonselingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PdsKonseling::with(['student.kelas']);

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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
        // Get all students for the modal dropdown (grouped by class for better UX potentially, or just list all)
        // Here we'll just get all active students to populate the select2/dropdown
        $students = Student::with('kelas')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'text' => $student->nis . ' - ' . $student->name . ' (' . ($student->kelas->nm_kls ?? '-') . ')',
                    'kelas_id' => $student->kelas_id
                ];
            });

        return view('admin.kesiswaan.konseling.index', compact('konseling', 'kelasList', 'students'));
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
            'status' => 'required|in:pending,diproses,selesai',
        ], [
            'student_id.required' => 'Siswa wajib dipilih',
            'tanggal.required' => 'Tanggal wajib diisi',
            'permasalahan.required' => 'Permasalahan wajib diisi',
        ]);

        PdsKonseling::create([
            'student_id' => $request->student_id,
            'tanggal' => $request->tanggal,
            'permasalahan' => $request->permasalahan,
            'penanganan' => $request->penanganan,
            'hasil' => $request->hasil,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
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
