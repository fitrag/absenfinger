<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiswaTerlambat;
use App\Models\Student;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SiswaTerlambatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SiswaTerlambat::with(['student.kelas']);

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

        $siswaTerlambat = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam_datang', 'desc')
            ->paginate(15)
            ->withQueryString();

        $kelasList = Kelas::orderBy('nm_kls')->get();

        return view('admin.kesiswaan.siswa-terlambat.index', compact('siswaTerlambat', 'kelasList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'tanggal' => 'required|date',
            'jam_datang' => 'required',
            'jam_masuk_seharusnya' => 'required',
            'alasan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,diproses,selesai',
        ], [
            'student_id.required' => 'Siswa wajib dipilih',
            'tanggal.required' => 'Tanggal wajib diisi',
            'jam_datang.required' => 'Jam datang wajib diisi',
            'jam_masuk_seharusnya.required' => 'Jam masuk seharusnya wajib diisi',
        ]);

        // Get student data
        $student = Student::findOrFail($request->student_id);

        // Calculate lateness
        $jamMasuk = strtotime($request->jam_masuk_seharusnya);
        $jamDatang = strtotime($request->jam_datang);
        $keterlambatan = max(0, (int) (($jamDatang - $jamMasuk) / 60));

        // Create checktime from tanggal + jam_datang
        $checktime = $request->tanggal . ' ' . $request->jam_datang . ':00';

        // Save to siswa_terlambat table
        SiswaTerlambat::create([
            'student_id' => $request->student_id,
            'tanggal' => $request->tanggal,
            'jam_datang' => $request->jam_datang,
            'jam_masuk_seharusnya' => $request->jam_masuk_seharusnya,
            'keterlambatan_menit' => $keterlambatan,
            'alasan' => $request->alasan,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'created_by' => Session::get('user_id'),
        ]);

        // Also save to attendances table
        \App\Models\Attendance::create([
            'nis' => $student->nis,
            'checktime' => $checktime,
            'checktype' => 0, // Check-in
        ]);

        return redirect()->route('admin.kesiswaan.siswa-terlambat.index')
            ->with('success', 'Data siswa terlambat berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SiswaTerlambat $siswaTerlambat)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'tanggal' => 'required|date',
            'jam_datang' => 'required',
            'jam_masuk_seharusnya' => 'required',
            'alasan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,diproses,selesai',
        ]);

        // Calculate lateness
        $jamMasuk = strtotime($request->jam_masuk_seharusnya);
        $jamDatang = strtotime($request->jam_datang);
        $keterlambatan = max(0, (int) (($jamDatang - $jamMasuk) / 60));

        $siswaTerlambat->update([
            'student_id' => $request->student_id,
            'tanggal' => $request->tanggal,
            'jam_datang' => $request->jam_datang,
            'jam_masuk_seharusnya' => $request->jam_masuk_seharusnya,
            'keterlambatan_menit' => $keterlambatan,
            'alasan' => $request->alasan,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.kesiswaan.siswa-terlambat.index')
            ->with('success', 'Data siswa terlambat berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SiswaTerlambat $siswaTerlambat)
    {
        $siswaTerlambat->delete();

        return redirect()->route('admin.kesiswaan.siswa-terlambat.index')
            ->with('success', 'Data siswa terlambat berhasil dihapus');
    }

    /**
     * Get students who haven't checked in yet for AJAX.
     */
    public function getLateStudents(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = $request->tanggal;
        $kelasId = $request->kelas_id;

        // Get NIS of students who have already checked in on this date
        $checkedInNis = \App\Models\Attendance::whereDate('checktime', $tanggal)
            ->where('checktype', 0)
            ->pluck('nis')
            ->toArray();

        // Get students who haven't checked in yet
        $query = Student::with('kelas')
            ->where('is_active', true)
            ->whereNotIn('nis', $checkedInNis);

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
}
