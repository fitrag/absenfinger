<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FileSoal;
use App\Models\Mapel;

use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileSoalController extends Controller
{
    public function index(Request $request)
    {
        $query = FileSoal::with(['mapel', 'guru', 'tahunPelajaran']);

        // Filter by jenis (MID/US)
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter by mapel
        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        }

        // Filter by kelas
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by tp_id
        $activeTp = TahunPelajaran::where('is_active', true)->first();
        $tpId = $request->filled('tp_id') ? $request->tp_id : ($activeTp ? $activeTp->id : null);
        if ($tpId) {
            $query->where('tp_id', $tpId);
        }

        $fileSoals = $query->latest()->paginate(15);
        $mapels = Mapel::orderBy('nm_mapel')->get();
        $tahunPelajarans = TahunPelajaran::orderBy('nm_tp', 'desc')->get();

        return view('admin.soal.file-soal.index', compact(
            'fileSoals',
            'mapels',
            'tahunPelajarans',
            'activeTp'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_file' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'jenis' => 'required|in:MID,US',
            'mapel_id' => 'required|exists:m_mapels,id',
            'tingkat' => 'required|in:X,XI,XII',
            'tp_id' => 'required|exists:m_tp,id',
            'semester' => 'required|in:Ganjil,Genap',
            'keterangan' => 'nullable|string',
        ]);

        // Get guru_id from session, or find from logged in user
        $guruId = session('guru_id');
        if (!$guruId) {
            // Try to get guru from current user
            $guru = \App\Models\Guru::where('user_id', auth()->id())->first();
            $guruId = $guru ? $guru->id : null;
        }

        // Upload file
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('soal', $fileName, 'public');

        FileSoal::create([
            'nama_file' => $request->nama_file,
            'file_path' => $filePath,
            'jenis' => $request->jenis,
            'mapel_id' => $request->mapel_id,
            'tingkat' => $request->tingkat,
            'guru_id' => $guruId,
            'tp_id' => $request->tp_id,
            'semester' => $request->semester,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'File soal berhasil diupload.');
    }

    public function destroy($id)
    {
        $fileSoal = FileSoal::findOrFail($id);

        // Delete file from storage
        if (Storage::disk('public')->exists($fileSoal->file_path)) {
            Storage::disk('public')->delete($fileSoal->file_path);
        }

        $fileSoal->delete();

        return back()->with('success', 'File soal berhasil dihapus.');
    }

    public function download($id)
    {
        $fileSoal = FileSoal::findOrFail($id);
        $filePath = storage_path('app/public/' . $fileSoal->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // Get original extension from file_path
        $extension = pathinfo($fileSoal->file_path, PATHINFO_EXTENSION);
        $downloadName = $fileSoal->nama_file;

        // Add extension if not already present
        if (!str_ends_with(strtolower($downloadName), '.' . strtolower($extension))) {
            $downloadName .= '.' . $extension;
        }

        return response()->download($filePath, $downloadName);
    }
}

