<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiswaTerlambat;
use App\Models\PdsPelanggaran;
use App\Models\PdsKonseling;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\TahunPelajaran;
use App\Models\Setting;
use Illuminate\Http\Request;

class RaporPDSController extends Controller
{
    /**
     * Display the Rapor PDS page with filters and summary.
     */
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('nm_kls')->get();
        $tpList = TahunPelajaran::orderBy('nm_tp', 'desc')->get();
        $tpAktif = TahunPelajaran::where('is_active', true)->first();

        $data = null;
        $kelasInfo = null;
        $tpInfo = null;

        // Only fetch data if filters are applied
        if ($request->filled('kelas_id') && $request->filled('tp_id') && $request->filled('semester')) {
            $kelasId = $request->kelas_id;
            $tpId = $request->tp_id;
            $semester = $request->semester;

            $kelasInfo = Kelas::find($kelasId);
            $tpInfo = TahunPelajaran::find($tpId);

            // Get students from the selected class
            $studentIds = Student::where('kelas_id', $kelasId)->pluck('id');

            // Keterlambatan data
            $keterlambatan = SiswaTerlambat::with(['student.kelas'])
                ->where('tp_id', $tpId)
                ->where('semester', $semester)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->groupBy('student_id')
                ->map(function ($items, $studentId) {
                    $student = $items->first()->student;
                    return (object) [
                        'student' => $student,
                        'total_terlambat' => $items->count(),
                        'total_menit' => $items->sum('keterlambatan_menit'),
                        'items' => $items
                    ];
                })->values();

            // Pelanggaran data
            $pelanggaran = PdsPelanggaran::with(['student.kelas'])
                ->where('tp_id', $tpId)
                ->where('semester', $semester)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->groupBy('student_id')
                ->map(function ($items, $studentId) {
                    $student = $items->first()->student;
                    return (object) [
                        'student' => $student,
                        'total_pelanggaran' => $items->count(),
                        'total_poin' => $items->sum('poin'),
                        'items' => $items
                    ];
                })->values();

            // Konseling data
            $konseling = PdsKonseling::with(['student.kelas'])
                ->where('tp_id', $tpId)
                ->where('semester', $semester)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->groupBy('student_id')
                ->map(function ($items, $studentId) {
                    $student = $items->first()->student;
                    return (object) [
                        'student' => $student,
                        'total_konseling' => $items->count(),
                        'items' => $items
                    ];
                })->values();

            $data = (object) [
                'keterlambatan' => $keterlambatan,
                'pelanggaran' => $pelanggaran,
                'konseling' => $konseling,
                'summary' => (object) [
                    'total_siswa_terlambat' => $keterlambatan->count(),
                    'total_keterlambatan' => $keterlambatan->sum('total_terlambat'),
                    'total_menit' => $keterlambatan->sum('total_menit'),
                    'total_siswa_pelanggaran' => $pelanggaran->count(),
                    'total_pelanggaran' => $pelanggaran->sum('total_pelanggaran'),
                    'total_poin' => $pelanggaran->sum('total_poin'),
                    'total_siswa_konseling' => $konseling->count(),
                    'total_konseling' => $konseling->sum('total_konseling'),
                ]
            ];
        }

        return view('admin.kesiswaan.rapor-pds.index', compact(
            'kelasList',
            'tpList',
            'tpAktif',
            'data',
            'kelasInfo',
            'tpInfo'
        ));
    }

    /**
     * Print Rapor PDS.
     */
    public function print(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tp_id' => 'required|exists:m_tp,id',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        $kelasId = $request->kelas_id;
        $tpId = $request->tp_id;
        $semester = $request->semester;

        $kelasInfo = Kelas::find($kelasId);
        $tpInfo = TahunPelajaran::find($tpId);
        $settings = Setting::getAllSettings();

        // Get students from the selected class
        $studentIds = Student::where('kelas_id', $kelasId)->pluck('id');

        // Keterlambatan data
        $keterlambatan = SiswaTerlambat::with(['student.kelas'])
            ->where('tp_id', $tpId)
            ->where('semester', $semester)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id')
            ->map(function ($items, $studentId) {
                $student = $items->first()->student;
                return (object) [
                    'student' => $student,
                    'total_terlambat' => $items->count(),
                    'total_menit' => $items->sum('keterlambatan_menit'),
                    'items' => $items
                ];
            })
            ->sortBy(fn($item) => $item->student->name)
            ->values();

        // Pelanggaran data
        $pelanggaran = PdsPelanggaran::with(['student.kelas'])
            ->where('tp_id', $tpId)
            ->where('semester', $semester)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id')
            ->map(function ($items, $studentId) {
                $student = $items->first()->student;
                return (object) [
                    'student' => $student,
                    'total_pelanggaran' => $items->count(),
                    'total_poin' => $items->sum('poin'),
                    'items' => $items
                ];
            })
            ->sortBy(fn($item) => $item->student->name)
            ->values();

        // Konseling data
        $konseling = PdsKonseling::with(['student.kelas'])
            ->where('tp_id', $tpId)
            ->where('semester', $semester)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id')
            ->map(function ($items, $studentId) {
                $student = $items->first()->student;
                return (object) [
                    'student' => $student,
                    'total_konseling' => $items->count(),
                    'items' => $items
                ];
            })
            ->sortBy(fn($item) => $item->student->name)
            ->values();

        return view('admin.kesiswaan.rapor-pds.print', compact(
            'kelasInfo',
            'tpInfo',
            'semester',
            'settings',
            'keterlambatan',
            'pelanggaran',
            'konseling'
        ));
    }

    /**
     * Print Rapor PDS for individual student.
     */
    public function printStudent(Request $request, Student $student)
    {
        $request->validate([
            'tp_id' => 'required|exists:m_tp,id',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        $tpId = $request->tp_id;
        $semester = $request->semester;

        $tpInfo = TahunPelajaran::find($tpId);
        $settings = Setting::getAllSettings();

        // Keterlambatan data
        $keterlambatan = SiswaTerlambat::where('tp_id', $tpId)
            ->where('semester', $semester)
            ->where('student_id', $student->id)
            ->orderBy('tanggal')
            ->get();

        // Pelanggaran data
        $pelanggaran = PdsPelanggaran::where('tp_id', $tpId)
            ->where('semester', $semester)
            ->where('student_id', $student->id)
            ->orderBy('tanggal')
            ->get();

        // Konseling data
        $konseling = PdsKonseling::where('tp_id', $tpId)
            ->where('semester', $semester)
            ->where('student_id', $student->id)
            ->orderBy('tanggal')
            ->get();

        return view('admin.kesiswaan.rapor-pds.print-student', compact(
            'student',
            'tpInfo',
            'semester',
            'settings',
            'keterlambatan',
            'pelanggaran',
            'konseling'
        ));
    }
}
