<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PdsPelanggaran;
use App\Models\Student;
use Illuminate\Support\Facades\Session;

class SiswaPelanggaranController extends Controller
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
     * Display pelanggaran list for the logged-in student.
     */
    public function index()
    {
        $student = $this->getStudent();

        if (!$student) {
            abort(403, 'Data siswa tidak ditemukan');
        }

        // Get all pelanggaran for this student
        $pelanggarans = PdsPelanggaran::where('student_id', $student->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Calculate total poin
        $totalPoin = $pelanggarans->sum('poin');
        $totalPelanggaran = $pelanggarans->count();

        return view('siswa.pelanggaran.index', compact(
            'student',
            'pelanggarans',
            'totalPoin',
            'totalPelanggaran'
        ));
    }
}
