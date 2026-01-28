<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiswaTerlambat;
use App\Models\PdsKonseling;
use App\Models\PdsPelanggaran;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\TahunPelajaran;
use App\Models\Setting;
use Illuminate\Http\Request;

class KesiswaanViewController extends Controller
{
    /**
     * Display read-only list of late students.
     */
    public function siswaTerlambat(Request $request)
    {
        // Get active TP from Settings
        $activeTpId = Setting::get('active_academic_year');
        $tpAktif = $activeTpId ? TahunPelajaran::find($activeTpId) : null;

        if (!$tpAktif) {
            $tpAktif = TahunPelajaran::where('is_active', true)->first();
        }

        $query = SiswaTerlambat::with(['student.kelas']);

        // Default: filter by active TP
        if ($tpAktif) {
            $query->where('tp_id', $tpAktif->id);
        }

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

        // Get all data and group by student
        $allData = $query->orderBy('tanggal', 'desc')->get();

        $groupedData = $allData->groupBy(function ($item) {
            return $item->student->name;
        })->map(function ($items, $studentName) {
            $firstItem = $items->first();
            return [
                'student_id' => $firstItem->student_id,
                'student_name' => $studentName,
                'student_nis' => $firstItem->student->nis,
                'student_kelas' => $firstItem->student->kelas->nm_kls ?? '-',
                'total_terlambat' => $items->count(),
                'total_menit' => $items->sum('keterlambatan_menit'),
                'items' => $items,
            ];
        })->sortByDesc('total_terlambat');

        $kelasList = Kelas::orderBy('nm_kls')->get();

        return view('admin.kesiswaan-view.siswa-terlambat', compact(
            'groupedData',
            'kelasList',
            'tpAktif'
        ));
    }

    /**
     * Display read-only list of counseling records.
     */
    public function konseling(Request $request)
    {
        $query = PdsKonseling::with(['student.kelas']);

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

        $konselingData = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $kelasList = Kelas::orderBy('nm_kls')->get();

        return view('admin.kesiswaan-view.konseling', compact(
            'konselingData',
            'kelasList'
        ));
    }

    /**
     * Display read-only list of violations.
     */
    public function pelanggaran(Request $request)
    {
        $query = PdsPelanggaran::with(['student.kelas']);

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

        // Get all data and group by student
        $allData = $query->orderBy('tanggal', 'desc')->get();

        $groupedData = $allData->groupBy(function ($item) {
            return $item->student_id;
        })->map(function ($items) {
            $firstItem = $items->first();
            $student = Student::with('kelas')->find($firstItem->student_id);

            $types = $items->pluck('jenis_pelanggaran')->unique()->toArray();

            return [
                'student' => $student,
                'total_poin' => $items->sum('poin'),
                'jumlah_pelanggaran' => $items->count(),
                'jenis_pelanggaran' => $types,
                'items' => $items,
            ];
        })->sortByDesc('total_poin');

        $kelasList = Kelas::orderBy('nm_kls')->get();

        return view('admin.kesiswaan-view.pelanggaran', compact(
            'groupedData',
            'kelasList'
        ));
    }
}
