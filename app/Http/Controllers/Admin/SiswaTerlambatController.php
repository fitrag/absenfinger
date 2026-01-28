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

        // Get all data (without pagination for grouping)
        $allData = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam_datang', 'desc')
            ->get();

        // Group data by student name
        $groupedData = $allData->groupBy(function ($item) {
            return $item->student->name;
        })->map(function ($items, $studentName) {
            $firstItem = $items->first();
            return [
                'student_name' => $studentName,
                'student_nis' => $firstItem->student->nis,
                'student_kelas' => $firstItem->student->kelas->nm_kls ?? '-',
                'total_terlambat' => $items->count(),
                'total_menit' => $items->sum('keterlambatan_menit'),
                'items' => $items,
            ];
        })->sortByDesc('total_terlambat');

        $kelasList = Kelas::orderBy('nm_kls')->get();

        // Get stats counts (using same query base for consistency with filters)
        $baseQuery = SiswaTerlambat::query();
        if ($request->filled('tanggal')) {
            $baseQuery->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('kelas_id')) {
            $baseQuery->whereHas('student', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }
        if ($request->filled('search')) {
            $baseQuery->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        $totalCount = (clone $baseQuery)->count();
        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $diprosesCount = (clone $baseQuery)->where('status', 'diproses')->count();
        $selesaiCount = (clone $baseQuery)->where('status', 'selesai')->count();

        return view('admin.kesiswaan.siswa-terlambat.index', compact(
            'groupedData',
            'kelasList',
            'totalCount',
            'pendingCount',
            'diprosesCount',
            'selesaiCount'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'tanggal' => 'required|date',
            'jam_datang' => 'required',
            'jam_masuk_seharusnya' => 'required',
            'alasan' => 'nullable|array',
            'alasan.*' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,diproses,selesai',
        ], [
            'student_ids.required' => 'Pilih minimal 1 siswa',
            'student_ids.min' => 'Pilih minimal 1 siswa',
            'tanggal.required' => 'Tanggal wajib diisi',
            'jam_datang.required' => 'Jam datang wajib diisi',
            'jam_masuk_seharusnya.required' => 'Jam masuk seharusnya wajib diisi',
        ]);

        // Calculate lateness
        $jamMasuk = strtotime($request->jam_masuk_seharusnya);
        $jamDatang = strtotime($request->jam_datang);
        $keterlambatan = max(0, (int) (($jamDatang - $jamMasuk) / 60));

        // Create checktime from tanggal + jam_datang
        $checktime = $request->tanggal . ' ' . $request->jam_datang . ':00';

        // Get alasan array
        $alasanList = $request->alasan ?? [];

        $count = 0;
        foreach ($request->student_ids as $studentId) {
            // Get student data
            $student = Student::findOrFail($studentId);

            // Get alasan for this specific student
            $alasan = $alasanList[$studentId] ?? null;

            // Save to siswa_terlambat table
            SiswaTerlambat::create([
                'student_id' => $studentId,
                'tanggal' => $request->tanggal,
                'jam_datang' => $request->jam_datang,
                'jam_masuk_seharusnya' => $request->jam_masuk_seharusnya,
                'keterlambatan_menit' => $keterlambatan,
                'alasan' => $alasan,
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

            $count++;
        }

        return redirect()->route('admin.kesiswaan.siswa-terlambat.index')
            ->with('success', "Data {$count} siswa terlambat berhasil ditambahkan");
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

        // Get NIS of students who have already checked in on this specific date
        $checkedInNis = \App\Models\Attendance::whereDate('checktime', $tanggal)
            ->where('checktype', 0)
            ->pluck('nis')
            ->toArray();

        // Get student IDs who are already recorded as late on this specific date
        $alreadyLateStudentIds = SiswaTerlambat::whereDate('tanggal', $tanggal)
            ->pluck('student_id')
            ->toArray();

        // Get all active students, excluding:
        // 1. Those who already checked in (attendance) on this date
        // 2. Those already recorded as late on this date
        $query = Student::with('kelas')
            ->where('is_active', true)
            ->whereNotIn('nis', $checkedInNis)
            ->whereNotIn('id', $alreadyLateStudentIds);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $students = $query->orderBy('name')->get()->map(function ($student) {
            // Get total late count for this student
            $jumlahTerlambat = SiswaTerlambat::where('student_id', $student->id)->count();

            return [
                'id' => $student->id,
                'nis' => $student->nis,
                'name' => $student->name,
                'kelas' => $student->kelas->nm_kls ?? '-',
                'jumlah_terlambat' => $jumlahTerlambat,
            ];
        });

        return response()->json($students);
    }

    /**
     * Display rekap siswa terlambat grouped by student.
     */
    public function rekapSiswa(Request $request)
    {
        $query = SiswaTerlambat::with(['student.kelas'])
            ->selectRaw('student_id, COUNT(*) as total_terlambat, SUM(keterlambatan_menit) as total_menit')
            ->groupBy('student_id');

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Search by student name or NIS
        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        $rekapSiswa = $query->orderByDesc('total_terlambat')
            ->get()
            ->map(function ($item) {
                $student = Student::with('kelas')->find($item->student_id);
                return (object) [
                    'student_id' => $item->student_id,
                    'student' => $student,
                    'total_terlambat' => $item->total_terlambat,
                    'total_menit' => $item->total_menit,
                ];
            });

        $kelasList = Kelas::orderBy('nm_kls')->get();

        return view('admin.kesiswaan.siswa-terlambat.rekap', compact('rekapSiswa', 'kelasList'));
    }

    /**
     * Print late records for a specific student.
     */
    public function printByStudent(Student $student)
    {
        // Get all late records for this student
        $lateRecords = SiswaTerlambat::with(['student.kelas'])
            ->where('student_id', $student->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Calculate totals
        $totalTerlambat = $lateRecords->count();
        $totalMenit = $lateRecords->sum('keterlambatan_menit');

        // Get settings for school info
        $settings = \App\Models\Setting::first();

        return view('admin.kesiswaan.siswa-terlambat.print', compact(
            'student',
            'lateRecords',
            'totalTerlambat',
            'totalMenit',
            'settings'
        ));
    }
}
