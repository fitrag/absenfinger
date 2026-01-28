<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PdsKonseling;
use App\Models\Student;
use Illuminate\Support\Facades\Session;

class SiswaKonselingController extends Controller
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
     * Display konseling list for the logged-in student.
     */
    public function index()
    {
        $student = $this->getStudent();

        if (!$student) {
            abort(403, 'Data siswa tidak ditemukan');
        }

        // Get all konseling for this student
        $konselings = PdsKonseling::where('student_id', $student->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Calculate stats
        $totalKonseling = $konselings->count();
        $totalSelesai = $konselings->where('status', 'selesai')->count();
        $totalDiproses = $konselings->where('status', 'diproses')->count();

        return view('siswa.konseling.index', compact(
            'student',
            'konselings',
            'totalKonseling',
            'totalSelesai',
            'totalDiproses'
        ));
    }
}
