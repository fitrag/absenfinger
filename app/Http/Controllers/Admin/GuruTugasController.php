<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruAjar;
use App\Models\Kelas;
use App\Models\KelasAjar;
use App\Models\Mapel;
use App\Models\Setting;
use App\Models\TahunPelajaran;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class GuruTugasController extends Controller
{
    public function index(Request $request)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->firstOrFail();

        // Get common data
        $tpList = TahunPelajaran::all();
        $activeTp = TahunPelajaran::where('is_active', 1)->first();
        $activeSemester = Setting::get('active_semester', 'Ganjil');

        // Get mapel yang diajar oleh guru ini dari GuruAjar
        $guruMapelIds = GuruAjar::where('guru_id', $guru->id)
            ->where('is_active', true)
            ->pluck('mapel_id');
        $mapelList = Mapel::whereIn('id', $guruMapelIds)
            ->orderBy('nm_mapel')
            ->get();

        // Filter parameters
        $tpId = $request->get('tp_id', $activeTp ? $activeTp->id : null);
        $semester = $request->get('semester');
        $mapelId = $request->get('mapel_id');
        $kelasId = $request->get('kelas_id');

        // Kelas list for filter - hanya kelas yang diajar guru
        $guruKelasIds = KelasAjar::where('guru_id', $guru->id)
            ->where('is_active', true)
            ->pluck('kelas_id');
        $kelasList = Kelas::whereIn('id', $guruKelasIds)
            ->orderBy('nm_kls', 'asc')
            ->get();

        $query = Tugas::with(['tahunPelajaran', 'mapel', 'kelas', 'submissions'])
            ->where('guru_id', $guru->id);

        if ($tpId) {
            $query->where('tp_id', $tpId);
        }
        if ($semester) {
            $query->where('semester', $semester);
        }
        if ($mapelId) {
            $query->where('mapel_id', $mapelId);
        }
        if ($kelasId) {
            $query->whereHas('kelas', function ($q) use ($kelasId) {
                $q->where('kelas.id', $kelasId);
            });
        }

        $tugasList = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.guru.tugas.index', compact(
            'tugasList',
            'tpList',
            'mapelList',
            'kelasList',
            'activeTp',
            'tpId',
            'semester',
            'mapelId',
            'kelasId',
            'guru',
            'activeSemester'
        ));
    }

    public function store(Request $request)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->firstOrFail();

        $request->validate([
            'tp_id' => 'required|exists:m_tp,id',
            'semester' => 'required|in:Ganjil,Genap',
            'mapel_id' => 'required|exists:m_mapels,id',
            'kelas_ids' => 'required|array|min:1',
            'kelas_ids.*' => 'exists:kelas,id',
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB
            'tanggal_deadline' => 'required|date',
            'jam_deadline' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // Create tugas first without file
            $tugas = Tugas::create([
                'guru_id' => $guru->id,
                'tp_id' => $request->tp_id,
                'semester' => $request->semester,
                'mapel_id' => $request->mapel_id,
                'judul' => $request->judul,
                'keterangan' => $request->keterangan,
                'file_path' => null,
                'tanggal_deadline' => $request->tanggal_deadline,
                'jam_deadline' => $request->jam_deadline,
            ]);

            // Sync kelas (Many to Many)
            $tugas->kelas()->attach($request->kelas_ids);

            // Upload file directly to tugas/{id}/ folder after tugas is created
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                // Use public disk explicitly so file is stored in storage/app/public/
                $filePath = $file->storeAs('tugas/' . $tugas->id, $filename, 'public');
                $tugas->update(['file_path' => $filePath]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Tugas berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($filePath) && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
            return redirect()->back()->with('error', 'Gagal membuat tugas: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->firstOrFail();
        $tugas = Tugas::where('id', $id)->where('guru_id', $guru->id)->firstOrFail();

        $request->validate([
            'tp_id' => 'required|exists:m_tp,id',
            'semester' => 'required|in:Ganjil,Genap',
            'mapel_id' => 'required|exists:m_mapels,id',
            'kelas_ids' => 'required|array|min:1',
            'kelas_ids.*' => 'exists:kelas,id',
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'tanggal_deadline' => 'required|date',
            'jam_deadline' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'tp_id' => $request->tp_id,
                'semester' => $request->semester,
                'mapel_id' => $request->mapel_id,
                'judul' => $request->judul,
                'keterangan' => $request->keterangan,
                'tanggal_deadline' => $request->tanggal_deadline,
                'jam_deadline' => $request->jam_deadline,
            ];

            if ($request->hasFile('file')) {
                // Delete old file
                if ($tugas->file_path && Storage::exists($tugas->file_path)) {
                    Storage::delete($tugas->file_path);
                }

                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/tugas/' . $tugas->id, $filename);
                $data['file_path'] = $path;
            }

            $tugas->update($data);
            $tugas->kelas()->sync($request->kelas_ids);

            DB::commit();
            return redirect()->back()->with('success', 'Tugas berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui tugas: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->firstOrFail();
        $tugas = Tugas::where('id', $id)->where('guru_id', $guru->id)->firstOrFail();

        try {
            // Delete assignment file
            if ($tugas->file_path && Storage::exists($tugas->file_path)) {
                Storage::delete($tugas->file_path);
            }

            // Delete directory if empty
            if (Storage::exists('public/tugas/' . $tugas->id)) {
                Storage::deleteDirectory('public/tugas/' . $tugas->id);
            }
            // Note: submissions files should also be deleted.
            // But since submissions are cascade deleted from DB, we might leave files or handle here.
            // Ideally we iterate submissions and delete their files too.
            foreach ($tugas->submissions as $submission) {
                if ($submission->file_path && Storage::exists($submission->file_path)) {
                    Storage::delete($submission->file_path);
                }
            }
            if (Storage::exists('public/tugas_submissions/' . $tugas->id)) {
                Storage::deleteDirectory('public/tugas_submissions/' . $tugas->id);
            }


            $tugas->delete();
            return redirect()->back()->with('success', 'Tugas berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus tugas: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->firstOrFail();
        $tugas = Tugas::with(['kelas', 'submissions.student', 'mapel', 'tahunPelajaran'])
            ->where('id', $id)
            ->where('guru_id', $guru->id)
            ->firstOrFail();

        // Get all students from assigned classes - flatMap flattens the collection
        // Assuming kelas->students relationship exists
        // Wait, Kelas model might not have students directly or we need to query students
        // Let's check logic: We need list of all students who SHOULD submit
        $kelasIds = $tugas->kelas->pluck('id');

        // Using existing relationships or query
        // Assuming Student model links to Kelas
        $students = \App\Models\Student::whereIn('kelas_id', $kelasIds)
            ->orderBy('name')
            ->get();

        // Map submissions by student_id for easy lookup
        $submissionsMap = $tugas->submissions->keyBy('student_id');

        return view('admin.guru.tugas.show', compact('tugas', 'students', 'submissionsMap'));
    }

    /**
     * Get kelas by mapel for the current guru (API endpoint)
     */
    public function getKelasByMapel(Request $request)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return response()->json([], 404);
        }

        $mapelId = $request->get('mapel_id');
        $tpId = $request->get('tp_id');

        if (!$mapelId) {
            return response()->json([]);
        }

        // Get kelas yang diajar guru untuk mapel ini
        $query = KelasAjar::with('kelas')
            ->where('guru_id', $guru->id)
            ->where('m_mapel_id', $mapelId)
            ->where('is_active', true);

        if ($tpId) {
            $query->where('tp_id', $tpId);
        }

        $kelasAjar = $query->get();

        $kelasList = $kelasAjar->map(function ($item) {
            return [
                'id' => $item->kelas->id,
                'nm_kls' => $item->kelas->nm_kls,
            ];
        })->unique('id')->values();

        return response()->json($kelasList);
    }

    /**
     * Update nilai for a student's submission
     */
    public function updateNilai(Request $request, $id, $submissionId)
    {
        $userId = Session::get('user_id');
        $guru = Guru::where('user_id', $userId)->firstOrFail();

        // Verify tugas belongs to this guru
        $tugas = Tugas::where('id', $id)->where('guru_id', $guru->id)->firstOrFail();

        // Verify submission belongs to this tugas
        $submission = TugasSubmission::where('id', $submissionId)
            ->where('tugas_id', $tugas->id)
            ->firstOrFail();

        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $submission->update([
            'nilai' => $request->nilai,
        ]);

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }
}
