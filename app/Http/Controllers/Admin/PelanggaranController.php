<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PdsPelanggaran;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\TahunPelajaran;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PelanggaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get active TP from Settings
        $activeTpId = Setting::get('active_academic_year');
        $tpAktif = $activeTpId ? TahunPelajaran::find($activeTpId) : null;

        // Fallback to is_active if not set in settings
        if (!$tpAktif) {
            $tpAktif = TahunPelajaran::where('is_active', true)->first();
        }

        // Get active semester from Settings
        $semesterAktif = Setting::get('active_semester') ?? 'Ganjil';

        $query = PdsPelanggaran::with(['student.kelas']);

        // Default: filter by active TP
        if ($tpAktif) {
            $query->where('tp_id', $tpAktif->id);
        }

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

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Search by student name or NIS
        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        $pelanggarans = $query->join('students', 'pds_pelanggarans.student_id', '=', 'students.id')
            ->orderBy('students.name', 'asc')
            ->orderBy('pds_pelanggarans.tanggal', 'desc')
            ->orderBy('pds_pelanggarans.created_at', 'desc')
            ->select('pds_pelanggarans.*')
            ->paginate(15)
            ->withQueryString();

        $kelasList = Kelas::orderBy('nm_kls')->get();
        $studentsList = Student::with('kelas')->where('is_active', true)->orderBy('name')->get();

        return view('admin.kesiswaan.pelanggaran.index', compact('pelanggarans', 'kelasList', 'studentsList', 'tpAktif', 'semesterAktif'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'tindakan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ttd_siswa' => 'nullable|string',
            'status' => 'required|in:pending,diproses,selesai',
            'tp_id' => 'nullable|exists:m_tp,id',
            'semester' => 'nullable|in:Ganjil,Genap',
        ], [
            'student_id.required' => 'Siswa wajib dipilih',
            'tanggal.required' => 'Tanggal wajib diisi',
            'jenis_pelanggaran.required' => 'Jenis pelanggaran wajib diisi',
            'poin.required' => 'Poin wajib diisi',
            'foto_bukti.image' => 'File harus berupa gambar',
            'foto_bukti.max' => 'Ukuran file maksimal 2MB',
        ]);

        // Handle photo upload with compression
        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $file = $request->file('foto_bukti');
            $filename = time() . '_' . uniqid() . '.jpg';
            $destinationPath = storage_path('app/public/pelanggaran/foto');

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Get image info
            $imageInfo = getimagesize($file->getRealPath());
            $mimeType = $imageInfo['mime'];

            // Create image resource based on mime type
            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($file->getRealPath());
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($file->getRealPath());
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($file->getRealPath());
                    break;
                default:
                    $image = imagecreatefromjpeg($file->getRealPath());
            }

            // Resize if image is too large (max 1920px width)
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);
            $maxWidth = 1920;

            if ($originalWidth > $maxWidth) {
                $ratio = $maxWidth / $originalWidth;
                $newWidth = $maxWidth;
                $newHeight = intval($originalHeight * $ratio);

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                imagedestroy($image);
                $image = $resizedImage;
            }

            // Compress with quality adjustment to target ~800KB
            $quality = 85; // Start with 85% quality
            $targetSize = 800 * 1024; // 800 KB in bytes
            $fullPath = $destinationPath . '/' . $filename;

            // Save with initial quality
            imagejpeg($image, $fullPath, $quality);

            // Reduce quality until file is under target size
            while (filesize($fullPath) > $targetSize && $quality > 30) {
                $quality -= 10;
                imagejpeg($image, $fullPath, $quality);
            }

            imagedestroy($image);
            $fotoPath = 'pelanggaran/foto/' . $filename;
        }

        PdsPelanggaran::create([
            'student_id' => $request->student_id,
            'tanggal' => $request->tanggal,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'poin' => $request->poin,
            'deskripsi' => $request->deskripsi,
            'tindakan' => $request->tindakan,
            'keterangan' => $request->keterangan,
            'foto_bukti' => $fotoPath,
            'ttd_siswa' => $request->ttd_siswa,
            'status' => $request->status,
            'tp_id' => $request->tp_id,
            'semester' => $request->semester,
            'created_by' => Session::get('user_id'),
        ]);

        return redirect()->route('admin.kesiswaan.pelanggaran.index')
            ->with('success', 'Data pelanggaran berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PdsPelanggaran $pelanggaran)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'tanggal' => 'required|date',
            'jenis_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'tindakan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ttd_siswa' => 'nullable|string',
            'status' => 'required|in:pending,diproses,selesai',
            'tp_id' => 'nullable|exists:m_tp,id',
            'semester' => 'nullable|in:Ganjil,Genap',
        ]);

        $updateData = [
            'student_id' => $request->student_id,
            'tanggal' => $request->tanggal,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'poin' => $request->poin,
            'deskripsi' => $request->deskripsi,
            'tindakan' => $request->tindakan,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'tp_id' => $request->tp_id,
            'semester' => $request->semester,
        ];

        // Handle photo upload with compression
        if ($request->hasFile('foto_bukti')) {
            // Delete old photo if exists
            if ($pelanggaran->foto_bukti && \Storage::disk('public')->exists($pelanggaran->foto_bukti)) {
                \Storage::disk('public')->delete($pelanggaran->foto_bukti);
            }

            $file = $request->file('foto_bukti');
            $filename = time() . '_' . uniqid() . '.jpg';
            $destinationPath = storage_path('app/public/pelanggaran/foto');

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Get image info
            $imageInfo = getimagesize($file->getRealPath());
            $mimeType = $imageInfo['mime'];

            // Create image resource based on mime type
            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($file->getRealPath());
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($file->getRealPath());
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($file->getRealPath());
                    break;
                default:
                    $image = imagecreatefromjpeg($file->getRealPath());
            }

            // Resize if image is too large (max 1920px width)
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);
            $maxWidth = 1920;

            if ($originalWidth > $maxWidth) {
                $ratio = $maxWidth / $originalWidth;
                $newWidth = $maxWidth;
                $newHeight = intval($originalHeight * $ratio);

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                imagedestroy($image);
                $image = $resizedImage;
            }

            // Compress with quality adjustment to target ~800KB
            $quality = 85;
            $targetSize = 800 * 1024;
            $fullPath = $destinationPath . '/' . $filename;

            imagejpeg($image, $fullPath, $quality);

            while (filesize($fullPath) > $targetSize && $quality > 30) {
                $quality -= 10;
                imagejpeg($image, $fullPath, $quality);
            }

            imagedestroy($image);
            $updateData['foto_bukti'] = 'pelanggaran/foto/' . $filename;
        }

        // Handle signature
        if ($request->filled('ttd_siswa')) {
            $updateData['ttd_siswa'] = $request->ttd_siswa;
        }

        // Set created_by if it's null (for old records)
        if (empty($pelanggaran->created_by)) {
            $updateData['created_by'] = Session::get('user_id');
        }

        $pelanggaran->update($updateData);

        return redirect()->route('admin.kesiswaan.pelanggaran.index')
            ->with('success', 'Data pelanggaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PdsPelanggaran $pelanggaran)
    {
        $pelanggaran->delete();

        return redirect()->route('admin.kesiswaan.pelanggaran.index')
            ->with('success', 'Data pelanggaran berhasil dihapus');
    }

    /**
     * Get students based on kelas for AJAX.
     */
    public function getStudents(Request $request)
    {
        $kelasId = $request->kelas_id;

        $query = Student::with('kelas')
            ->where('is_active', true);

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

    /**
     * Print pelanggaran by student.
     */
    public function printByStudent(Student $student)
    {
        $pelanggarans = PdsPelanggaran::with('creator')
            ->where('student_id', $student->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPoin = $pelanggarans->sum('poin');

        $settings = \App\Models\Setting::getAllSettings();

        return view('admin.kesiswaan.pelanggaran.print', compact('student', 'pelanggarans', 'totalPoin', 'settings'));
    }
}
