<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoalMid;
use App\Models\FileSoal;
use App\Models\Mapel;

use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class SoalMidController extends Controller
{
    public function index(Request $request)
    {
        $query = SoalMid::with(['mapel', 'guru', 'tahunPelajaran', 'fileSoal']);

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

        $soals = $query->orderBy('no_soal')->paginate(20);
        $mapels = Mapel::orderBy('nm_mapel')->get();
        
        $tahunPelajarans = TahunPelajaran::orderBy('nm_tp', 'desc')->get();
        $fileSoals = FileSoal::where('jenis', 'MID')->get();

        return view('admin.soal.soal-mid.index', compact(
            'soals',
            'mapels',
            'tahunPelajarans',
            'fileSoals',
            'activeTp'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mapel_id' => 'required|exists:m_mapels,id',
            'tingkat' => 'required|in:X,XI,XII',
            'tp_id' => 'required|exists:m_tp,id',
            'semester' => 'required|in:Ganjil,Genap',
            'no_soal' => 'required|integer|min:1',
            'pertanyaan' => 'required|string',
            'tipe_soal' => 'required|in:pilihan_ganda,essay',
            'opsi_a' => 'nullable|string',
            'opsi_b' => 'nullable|string',
            'opsi_c' => 'nullable|string',
            'opsi_d' => 'nullable|string',
            'opsi_e' => 'nullable|string',
            'jawaban_benar' => 'nullable|string',
            'bobot' => 'nullable|integer|min:1',
            'file_soal_id' => 'nullable|exists:file_soals,id',
        ]);

        $guruId = session('guru_id');
        if (!$guruId) {
            return back()->with('error', 'Anda tidak memiliki akses sebagai guru.');
        }

        SoalMid::create([
            'file_soal_id' => $request->file_soal_id,
            'mapel_id' => $request->mapel_id,
            'tingkat' => $request->tingkat,
            'guru_id' => $guruId,
            'tp_id' => $request->tp_id,
            'semester' => $request->semester,
            'no_soal' => $request->no_soal,
            'pertanyaan' => $request->pertanyaan,
            'opsi_a' => $request->opsi_a,
            'opsi_b' => $request->opsi_b,
            'opsi_c' => $request->opsi_c,
            'opsi_d' => $request->opsi_d,
            'opsi_e' => $request->opsi_e,
            'jawaban_benar' => $request->jawaban_benar,
            'bobot' => $request->bobot ?? 1,
            'tipe_soal' => $request->tipe_soal,
        ]);

        return back()->with('success', 'Soal MID berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $soal = SoalMid::findOrFail($id);

        $request->validate([
            'pertanyaan' => 'required|string',
            'tipe_soal' => 'required|in:pilihan_ganda,essay',
            'opsi_a' => 'nullable|string',
            'opsi_b' => 'nullable|string',
            'opsi_c' => 'nullable|string',
            'opsi_d' => 'nullable|string',
            'opsi_e' => 'nullable|string',
            'jawaban_benar' => 'nullable|string',
            'bobot' => 'nullable|integer|min:1',
        ]);

        $soal->update([
            'pertanyaan' => $request->pertanyaan,
            'opsi_a' => $request->opsi_a,
            'opsi_b' => $request->opsi_b,
            'opsi_c' => $request->opsi_c,
            'opsi_d' => $request->opsi_d,
            'opsi_e' => $request->opsi_e,
            'jawaban_benar' => $request->jawaban_benar,
            'bobot' => $request->bobot ?? 1,
            'tipe_soal' => $request->tipe_soal,
        ]);

        return back()->with('success', 'Soal MID berhasil diupdate.');
    }

    public function destroy($id)
    {
        $soal = SoalMid::findOrFail($id);
        $soal->delete();

        return back()->with('success', 'Soal MID berhasil dihapus.');
    }
}


