<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PklKompSoft;
use App\Models\PklKompHard;
use App\Models\PklKompWirausaha;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class PklKomponenNilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $jurusanId = $request->get('jurusan_id');
        $searchSoft = $request->get('search_soft');
        $searchHard = $request->get('search_hard');
        $searchWirausaha = $request->get('search_wirausaha');

        // Query for Soft Skills
        $querySoft = PklKompSoft::with('jurusan');
        if ($jurusanId) {
            $querySoft->where('m_jurusan_id', $jurusanId);
        }
        if ($searchSoft) {
            $querySoft->where('nama', 'like', "%{$searchSoft}%");
        }
        $kompSoftList = $querySoft->orderBy('m_jurusan_id')->orderBy('nama')->get();

        // Query for Hard Skills
        $queryHard = PklKompHard::with('jurusan');
        if ($jurusanId) {
            $queryHard->where('m_jurusan_id', $jurusanId);
        }
        if ($searchHard) {
            $queryHard->where('nama', 'like', "%{$searchHard}%");
        }
        $kompHardList = $queryHard->orderBy('m_jurusan_id')->orderBy('nama')->get();

        // Query for Wirausaha
        $queryWirausaha = PklKompWirausaha::with('jurusan');
        if ($jurusanId) {
            $queryWirausaha->where('m_jurusan_id', $jurusanId);
        }
        if ($searchWirausaha) {
            $queryWirausaha->where('nama', 'like', "%{$searchWirausaha}%");
        }
        $kompWirausahaList = $queryWirausaha->orderBy('m_jurusan_id')->orderBy('nama')->get();

        $jurusanList = Jurusan::orderBy('paket_keahlian')->get();

        $stats = [
            'totalSoft' => PklKompSoft::count(),
            'totalHard' => PklKompHard::count(),
            'totalWirausaha' => PklKompWirausaha::count(),
        ];

        return view('admin.pkl.komponen-nilai.index', compact(
            'kompSoftList',
            'kompHardList',
            'kompWirausahaList',
            'jurusanList',
            'jurusanId',
            'searchSoft',
            'searchHard',
            'searchWirausaha',
            'stats'
        ));
    }

    /**
     * Store a newly created soft skill komponen.
     */
    public function storeSoft(Request $request)
    {
        $request->validate([
            'm_jurusan_id' => 'required',
            'nama' => 'required|string|max:255',
        ], [
            'm_jurusan_id.required' => 'Jurusan wajib dipilih',
            'nama.required' => 'Nama komponen wajib diisi',
        ]);

        // If 'all' is selected, create for all jurusan
        if ($request->m_jurusan_id === 'all') {
            $jurusanList = Jurusan::all();
            foreach ($jurusanList as $jurusan) {
                PklKompSoft::create([
                    'm_jurusan_id' => $jurusan->id,
                    'nama' => $request->nama,
                ]);
            }
            return redirect()->route('admin.pkl.komponen-nilai.index')
                ->with('success', 'Komponen Soft Skill berhasil ditambahkan untuk semua jurusan');
        }

        PklKompSoft::create($request->only(['m_jurusan_id', 'nama']));

        return redirect()->route('admin.pkl.komponen-nilai.index')
            ->with('success', 'Komponen Soft Skill berhasil ditambahkan');
    }

    /**
     * Store a newly created hard skill komponen.
     */
    public function storeHard(Request $request)
    {
        $request->validate([
            'm_jurusan_id' => 'required',
            'nama' => 'required|string|max:255',
        ], [
            'm_jurusan_id.required' => 'Jurusan wajib dipilih',
            'nama.required' => 'Nama komponen wajib diisi',
        ]);

        // If 'all' is selected, create for all jurusan
        if ($request->m_jurusan_id === 'all') {
            $jurusanList = Jurusan::all();
            foreach ($jurusanList as $jurusan) {
                PklKompHard::create([
                    'm_jurusan_id' => $jurusan->id,
                    'nama' => $request->nama,
                ]);
            }
            return redirect()->route('admin.pkl.komponen-nilai.index', ['tab' => 'hard'])
                ->with('success', 'Komponen Hard Skill berhasil ditambahkan untuk semua jurusan');
        }

        PklKompHard::create($request->only(['m_jurusan_id', 'nama']));

        return redirect()->route('admin.pkl.komponen-nilai.index', ['tab' => 'hard'])
            ->with('success', 'Komponen Hard Skill berhasil ditambahkan');
    }

    /**
     * Store a newly created wirausaha komponen.
     */
    public function storeWirausaha(Request $request)
    {
        $request->validate([
            'm_jurusan_id' => 'required',
            'nama' => 'required|string|max:255',
        ], [
            'm_jurusan_id.required' => 'Jurusan wajib dipilih',
            'nama.required' => 'Nama komponen wajib diisi',
        ]);

        // If 'all' is selected, create for all jurusan
        if ($request->m_jurusan_id === 'all') {
            $jurusanList = Jurusan::all();
            foreach ($jurusanList as $jurusan) {
                PklKompWirausaha::create([
                    'm_jurusan_id' => $jurusan->id,
                    'nama' => $request->nama,
                ]);
            }
            return redirect()->route('admin.pkl.komponen-nilai.index', ['tab' => 'wirausaha'])
                ->with('success', 'Komponen Wirausaha berhasil ditambahkan untuk semua jurusan');
        }

        PklKompWirausaha::create($request->only(['m_jurusan_id', 'nama']));

        return redirect()->route('admin.pkl.komponen-nilai.index', ['tab' => 'wirausaha'])
            ->with('success', 'Komponen Wirausaha berhasil ditambahkan');
    }

    /**
     * Update a soft skill komponen.
     */
    public function updateSoft(Request $request, $id)
    {
        $request->validate([
            'm_jurusan_id' => 'required|exists:m_jurusan,id',
            'nama' => 'required|string|max:255',
        ]);

        $komponen = PklKompSoft::findOrFail($id);
        $komponen->update($request->only(['m_jurusan_id', 'nama']));

        return redirect()->route('admin.pkl.komponen-nilai.index')
            ->with('success', 'Komponen Soft Skill berhasil diperbarui');
    }

    /**
     * Update a hard skill komponen.
     */
    public function updateHard(Request $request, $id)
    {
        $request->validate([
            'm_jurusan_id' => 'required|exists:m_jurusan,id',
            'nama' => 'required|string|max:255',
        ]);

        $komponen = PklKompHard::findOrFail($id);
        $komponen->update($request->only(['m_jurusan_id', 'nama']));

        return redirect()->route('admin.pkl.komponen-nilai.index', ['tab' => 'hard'])
            ->with('success', 'Komponen Hard Skill berhasil diperbarui');
    }

    /**
     * Update a wirausaha komponen.
     */
    public function updateWirausaha(Request $request, $id)
    {
        $request->validate([
            'm_jurusan_id' => 'required|exists:m_jurusan,id',
            'nama' => 'required|string|max:255',
        ]);

        $komponen = PklKompWirausaha::findOrFail($id);
        $komponen->update($request->only(['m_jurusan_id', 'nama']));

        return redirect()->route('admin.pkl.komponen-nilai.index', ['tab' => 'wirausaha'])
            ->with('success', 'Komponen Wirausaha berhasil diperbarui');
    }

    /**
     * Remove a soft skill komponen.
     */
    public function destroySoft($id)
    {
        $komponen = PklKompSoft::findOrFail($id);
        $komponen->delete();

        return redirect()->route('admin.pkl.komponen-nilai.index')
            ->with('success', 'Komponen Soft Skill berhasil dihapus');
    }

    /**
     * Remove a hard skill komponen.
     */
    public function destroyHard($id)
    {
        $komponen = PklKompHard::findOrFail($id);
        $komponen->delete();

        return redirect()->route('admin.pkl.komponen-nilai.index', ['tab' => 'hard'])
            ->with('success', 'Komponen Hard Skill berhasil dihapus');
    }

    /**
     * Remove a wirausaha komponen.
     */
    public function destroyWirausaha($id)
    {
        $komponen = PklKompWirausaha::findOrFail($id);
        $komponen->delete();

        return redirect()->route('admin.pkl.komponen-nilai.index', ['tab' => 'wirausaha'])
            ->with('success', 'Komponen Wirausaha berhasil dihapus');
    }
}

