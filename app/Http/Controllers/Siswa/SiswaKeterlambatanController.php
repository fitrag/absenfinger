<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\SiswaTerlambat;
use App\Models\Student;
use Illuminate\Support\Facades\Session;

class SiswaKeterlambatanController extends Controller
{
    /**
     * Get the logged-in student.
     */
    private function getStudent()
    {
        $userId = Session::get('user_id');

        return Student::with('kelas')
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Display keterlambatan list for the logged-in student.
     */
    public function index()
    {
        $student = $this->getStudent();

        if (!$student) {
            abort(403, 'Data siswa tidak ditemukan');
        }

        // Get all keterlambatan for this student
        $keterlambatans = SiswaTerlambat::where('student_id', $student->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Calculate stats
        $totalKeterlambatan = $keterlambatans->count();
        $totalMenit = $keterlambatans->sum('durasi');
        $totalSelesai = $keterlambatans->where('status', 'selesai')->count();
        $totalDiproses = $keterlambatans->where('status', 'diproses')->count();

        return view('siswa.keterlambatan.index', compact(
            'student',
            'keterlambatans',
            'totalKeterlambatan',
            'totalMenit',
            'totalSelesai',
            'totalDiproses'
        ));
    }
}
