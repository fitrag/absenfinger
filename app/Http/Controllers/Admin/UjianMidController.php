<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UjianMid;
use App\Models\Mapel;

use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class UjianMidController extends Controller
{
    public function index(Request $request)
    {
        $query = UjianMid::with(['mapel', 'guru', 'tahunPelajaran']);

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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tp_id
        $activeTp = TahunPelajaran::where('is_active', true)->first();
        $tpId = $request->filled('tp_id') ? $request->tp_id : ($activeTp ? $activeTp->id : null);
        if ($tpId) {
            $query->where('tp_id', $tpId);
        }

        $ujians = $query->latest()->paginate(15);
        $mapels = Mapel::orderBy('nm_mapel')->get();
        
        $tahunPelajarans = TahunPelajaran::orderBy('nm_tp', 'desc')->get();

        return view('admin.soal.ujian-mid.index', compact(
            'ujians',
            'mapels',
            'tahunPelajarans',
            'activeTp'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ujian' => 'required|string|max:255',
            'mapel_id' => 'required|exists:m_mapels,id',
            'tingkat' => 'required|in:X,XI,XII',
            'tp_id' => 'required|exists:m_tp,id',
            'semester' => 'required|in:Ganjil,Genap',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'durasi' => 'required|integer|min:1',
        ]);

        $guruId = session('guru_id');
        if (!$guruId) {
            return back()->with('error', 'Anda tidak memiliki akses sebagai guru.');
        }

        UjianMid::create([
            'nama_ujian' => $request->nama_ujian,
            'mapel_id' => $request->mapel_id,
            'tingkat' => $request->tingkat,
            'guru_id' => $guruId,
            'tp_id' => $request->tp_id,
            'semester' => $request->semester,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'durasi' => $request->durasi,
            'status' => 'draft',
        ]);

        return back()->with('success', 'Ujian MID berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $ujian = UjianMid::findOrFail($id);

        $request->validate([
            'nama_ujian' => 'required|string|max:255',
            'mapel_id' => 'required|exists:m_mapels,id',
            'tingkat' => 'required|in:X,XI,XII',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'durasi' => 'required|integer|min:1',
            'status' => 'required|in:draft,aktif,selesai',
        ]);

        $ujian->update([
            'nama_ujian' => $request->nama_ujian,
            'mapel_id' => $request->mapel_id,
            'tingkat' => $request->tingkat,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'durasi' => $request->durasi,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Ujian MID berhasil diupdate.');
    }

    public function destroy($id)
    {
        $ujian = UjianMid::findOrFail($id);
        $ujian->delete();

        return back()->with('success', 'Ujian MID berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
    {
        $ujian = UjianMid::findOrFail($id);

        $request->validate([
            'status' => 'required|in:draft,aktif,selesai',
        ]);

        $ujian->update(['status' => $request->status]);

        return back()->with('success', 'Status ujian berhasil diupdate.');
    }
}


