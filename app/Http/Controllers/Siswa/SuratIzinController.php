<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\SuratIzin;
use App\Models\Student;
use App\Models\Pkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SuratIzinController extends Controller
{
    /**
     * Display a listing of the surat izin
     */
    public function index(Request $request)
    {
        $userId = Session::get('user_id');
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get active PKL
        $pkl = Pkl::where('student_id', $student->id)->first();

        $query = SuratIzin::where('student_id', $student->id)
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by jenis izin
        if ($request->filled('jenis_izin')) {
            $query->where('jenis_izin', $request->jenis_izin);
        }

        $suratIzins = $query->paginate(10);

        return view('siswa.surat-izin.index', compact('suratIzins', 'student', 'pkl'));
    }

    /**
     * Show the form for creating a new surat izin
     */
    public function create()
    {
        $userId = Session::get('user_id');
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get active PKL
        $pkl = Pkl::where('student_id', $student->id)->first();

        return view('siswa.surat-izin.create', compact('student', 'pkl'));
    }

    /**
     * Store a newly created surat izin
     */
    public function store(Request $request)
    {
        $userId = Session::get('user_id');
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_izin' => 'required|in:sakit,izin,lainnya',
            'keterangan' => 'required|string|max:1000',
            'file' => 'nullable|file|mimes:pdf|max:2048', // Max 2MB PDF
        ], [
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'jenis_izin.required' => 'Jenis izin wajib dipilih.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'Ukuran file maksimal 2MB.',
        ]);

        // Get active PKL
        $pkl = Pkl::where('student_id', $student->id)->first();

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'surat_izin_' . $student->nis . '_' . time() . '.pdf';
            $filePath = $file->storeAs('surat-izin', $fileName, 'public');
        }

        SuratIzin::create([
            'student_id' => $student->id,
            'pkl_id' => $pkl?->id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jenis_izin' => $request->jenis_izin,
            'keterangan' => $request->keterangan,
            'file_path' => $filePath,
            'status' => 'pending',
        ]);

        return redirect()->route('siswa.surat-izin.index')->with('success', 'Surat izin berhasil diajukan.');
    }

    /**
     * Display the specified surat izin
     */
    public function show($id)
    {
        $userId = Session::get('user_id');
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $suratIzin = SuratIzin::where('student_id', $student->id)
            ->where('id', $id)
            ->with(['pkl.dudi', 'approver'])
            ->firstOrFail();

        return view('siswa.surat-izin.show', compact('suratIzin', 'student'));
    }

    /**
     * Show the form for editing the specified surat izin
     */
    public function edit($id)
    {
        $userId = Session::get('user_id');
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $suratIzin = SuratIzin::where('student_id', $student->id)
            ->where('id', $id)
            ->where('status', 'pending') // Only pending can be edited
            ->firstOrFail();

        $pkl = Pkl::where('student_id', $student->id)->first();

        return view('siswa.surat-izin.edit', compact('suratIzin', 'student', 'pkl'));
    }

    /**
     * Update the specified surat izin
     */
    public function update(Request $request, $id)
    {
        $userId = Session::get('user_id');
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $suratIzin = SuratIzin::where('student_id', $student->id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_izin' => 'required|in:sakit,izin,lainnya',
            'keterangan' => 'required|string|max:1000',
            'file' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $filePath = $suratIzin->file_path;
        if ($request->hasFile('file')) {
            // Delete old file
            if ($suratIzin->file_path) {
                Storage::disk('public')->delete($suratIzin->file_path);
            }

            $file = $request->file('file');
            $fileName = 'surat_izin_' . $student->nis . '_' . time() . '.pdf';
            $filePath = $file->storeAs('surat-izin', $fileName, 'public');
        }

        $suratIzin->update([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jenis_izin' => $request->jenis_izin,
            'keterangan' => $request->keterangan,
            'file_path' => $filePath,
        ]);

        return redirect()->route('siswa.surat-izin.index')->with('success', 'Surat izin berhasil diperbarui.');
    }

    /**
     * Remove the specified surat izin
     */
    public function destroy($id)
    {
        $userId = Session::get('user_id');
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $suratIzin = SuratIzin::where('student_id', $student->id)
            ->where('id', $id)
            ->where('status', 'pending') // Only pending can be deleted
            ->firstOrFail();

        // Delete file if exists
        if ($suratIzin->file_path) {
            Storage::disk('public')->delete($suratIzin->file_path);
        }

        $suratIzin->delete();

        return redirect()->route('siswa.surat-izin.index')->with('success', 'Surat izin berhasil dihapus.');
    }

    /**
     * Download the surat izin file
     */
    public function download($id)
    {
        $userId = Session::get('user_id');
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $suratIzin = SuratIzin::where('student_id', $student->id)
            ->where('id', $id)
            ->firstOrFail();

        if (!$suratIzin->file_path || !Storage::disk('public')->exists($suratIzin->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($suratIzin->file_path);
        $fileName = basename($suratIzin->file_path);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}
