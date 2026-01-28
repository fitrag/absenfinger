<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SiswaTugasController extends Controller
{
    public function index(Request $request)
    {
        $userId = Session::get('user_id');
        $student = Student::where('user_id', $userId)->firstOrFail();

        // Get assignments for student's class
        $query = Tugas::whereHas('kelas', function ($q) use ($student) {
            $q->where('kelas.id', $student->kelas_id);
        })->with([
                    'mapel',
                    'guru',
                    'submissions' => function ($q) use ($student) {
                        $q->where('student_id', $student->id);
                    }
                ]);

        // Filter by status (optional)
        $status = $request->get('status', 'all'); // all, pending, submitted

        $tugasList = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('siswa.tugas.index', compact('tugasList', 'student'));
    }

    public function submit(Request $request, $id)
    {
        $userId = Session::get('user_id');
        $student = Student::where('user_id', $userId)->firstOrFail();
        $tugas = Tugas::findOrFail($id);

        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB, any file type allowed? User said PDF for task, maybe PDF/Image for submission? Let's allow common docs
            // 'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Check if already submitted
            $submission = TugasSubmission::where('tugas_id', $tugas->id)
                ->where('student_id', $student->id)
                ->first();

            if ($submission) {
                // Update existing submission? Or block? Usually re-submit is allowed before deadline
                // If deadline passed?
                // if ($tugas->isDeadlinePassed) {
                //      return back()->with('error', 'Maaf, waktu pengumpulan sudah habis.');
                // }

                // Delete old file from public disk
                if (Storage::disk('public')->exists($submission->file_path)) {
                    Storage::disk('public')->delete($submission->file_path);
                }
            } else {
                $submission = new TugasSubmission();
                $submission->tugas_id = $tugas->id;
                $submission->student_id = $student->id;
            }

            $file = $request->file('file');
            $filename = time() . '_' . $student->nisn . '_' . $file->getClientOriginalName();
            // Use public disk explicitly so file is accessible via URL
            $path = $file->storeAs('tugas_submissions/' . $tugas->id . '/' . $student->id, $filename, 'public');

            $submission->file_path = $path;
            $submission->keterangan = $request->keterangan;
            $submission->submitted_at = now();
            $submission->save();

            DB::commit();
            return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengumpulkan tugas: ' . $e->getMessage());
        }
    }
}
