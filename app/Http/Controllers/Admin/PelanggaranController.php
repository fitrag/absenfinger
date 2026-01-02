<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PdsPelanggaran;
use App\Models\Student;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PelanggaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PdsPelanggaran::with(['student.kelas']);

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

        $pelanggarans = $query->join('students', 'pds_pelanggarans.student_id', '=', 'students.id')
            ->orderBy('students.name', 'asc')
            ->orderBy('pds_pelanggarans.tanggal', 'desc')
            ->orderBy('pds_pelanggarans.created_at', 'desc')
            ->select('pds_pelanggarans.*')
            ->paginate(15)
            ->withQueryString();

        $kelasList = Kelas::orderBy('nm_kls')->get();
        $studentsList = Student::with('kelas')->where('is_active', true)->orderBy('name')->get();

        return view('admin.kesiswaan.pelanggaran.index', compact('pelanggarans', 'kelasList', 'studentsList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'tindakan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,diproses,selesai',
        ], [
            'student_id.required' => 'Siswa wajib dipilih',
            'tanggal.required' => 'Tanggal wajib diisi',
            'jenis_pelanggaran.required' => 'Jenis pelanggaran wajib diisi',
            'poin.required' => 'Poin wajib diisi',
        ]);

        PdsPelanggaran::create([
            'student_id' => $request->student_id,
            'tanggal' => $request->tanggal,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'poin' => $request->poin,
            'deskripsi' => $request->deskripsi,
            'tindakan' => $request->tindakan,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'created_by' => Session::get('user_id'),
        ]);

        return redirect()->route('admin.kesiswaan.pelanggaran.index')
            ->with('success', 'Data pelanggaran berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PdsPelanggaran $pelanggaran)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'tindakan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,diproses,selesai',
        ]);

        $updateData = [
            'student_id' => $request->student_id,
            'tanggal' => $request->tanggal,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'poin' => $request->poin,
            'deskripsi' => $request->deskripsi,
            'tindakan' => $request->tindakan,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
        ];

        // Set created_by if it's null (for old records)
        if (empty($pelanggaran->created_by)) {
            $updateData['created_by'] = Session::get('user_id');
        }

        $pelanggaran->update($updateData);

        return redirect()->route('admin.kesiswaan.pelanggaran.index')
            ->with('success', 'Data pelanggaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PdsPelanggaran $pelanggaran)
    {
        $pelanggaran->delete();

        return redirect()->route('admin.kesiswaan.pelanggaran.index')
            ->with('success', 'Data pelanggaran berhasil dihapus');
    }

    /**
     * Get students based on kelas for AJAX.
     */
    public function getStudents(Request $request)
    {
        $kelasId = $request->kelas_id;

        $query = Student::with('kelas')
            ->where('is_active', true);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $students = $query->orderBy('name')->get()->map(function ($student) {
            return [
                'id' => $student->id,
                'nis' => $student->nis,
                'name' => $student->name,
                'kelas' => $student->kelas->nm_kls ?? '-',
            ];
        });

        return response()->json($students);
    }

    /**
     * Print pelanggaran by student.
     */
    public function printByStudent(Student $student)
    {
        $pelanggarans = PdsPelanggaran::with('creator')
            ->where('student_id', $student->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPoin = $pelanggarans->sum('poin');

        $settings = \App\Models\Setting::getAllSettings();

        return view('admin.kesiswaan.pelanggaran.print', compact('student', 'pelanggarans', 'totalPoin', 'settings'));
    }
}
