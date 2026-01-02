<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruAjar;
use App\Models\Kelas;
use App\Models\KelasAjar;
use App\Models\Mapel;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GuruKelasAjarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Session::get('user_id');
        $userLevel = Session::get('user_level');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check level or role (matching GuruJurnal logic roughly, or strictly/permissive)
        // GuruJurnal uses: if ($userLevel !== 'guru')
        // We will be slightly more permissive if needed or strict. Let's start strict but safe.
        // Actually, if Sidebar allows 'Piket' role, we should check that too?
        // GuruJurnal strictly checks 'guru'. Let's follow that for now, or check Guru record existence.

        $guru = Guru::where('user_id', $userId)->first();

        // Ensure user is linked to a Guru
        if (!$guru) {
            return redirect()->back()->with('error', 'Akun anda tidak terhubung dengan data Guru.');
        }

        $activeTp = TahunPelajaran::active()->first();

        // Get all classes taught by this guru, ordered by latest TP
        $kelasAjar = KelasAjar::with(['kelas', 'tp', 'mapel'])
            ->where('guru_id', $guru->id)
            ->orderBy('tp_id', 'desc')
            ->orderBy('m_mapel_id', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        // Group by TP, then by Mapel for display
        $groupedKelasAjar = $kelasAjar->groupBy(function ($item) {
            return $item->tp->nm_tp ?? 'Unknown';
        })->map(function ($tpGroup) {
            return $tpGroup->groupBy(function ($item) {
                return $item->mapel->nm_mapel ?? 'Tanpa Mapel';
            });
        });

        // Data for Add Modal
        $gurus = [$guru];
        $kelasList = Kelas::orderBy('nm_kls')->get();

        // Get mapels assigned to this guru from m_guruajars table
        $guruMapelIds = GuruAjar::where('guru_id', $guru->id)
            ->where('is_active', true)
            ->pluck('mapel_id');
        $mapelList = Mapel::whereIn('id', $guruMapelIds)
            ->orderBy('nm_mapel')
            ->get();

        $tpList = TahunPelajaran::orderBy('nm_tp', 'desc')->get();

        // Get mapping of TP ID -> Mapel ID -> Array of Class IDs already assigned
        $existingAssignments = $kelasAjar->groupBy('tp_id')->map(function ($tpGroup) {
            return $tpGroup->groupBy('m_mapel_id')->map(function ($mapelGroup) {
                return $mapelGroup->pluck('kelas_id');
            });
        });

        // Get ALL active mapels for "Tambah Mapel" modal
        $allMapels = Mapel::where('is_active', true)->orderBy('nm_mapel')->get();

        return view('admin.guru.kelasajar.index', compact(
            'groupedKelasAjar',
            'kelasList',
            'mapelList',
            'allMapels',
            'tpList',
            'activeTp',
            'guru',
            'existingAssignments'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }
        $guruId = $guru->id;

        $request->validate([
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id',
            'tp_id' => 'required|exists:m_tp,id',
            'm_mapel_id' => 'required|exists:m_mapels,id',
        ], [
            'kelas_ids.required' => 'Minimal satu kelas harus dipilih',
            'tp_id.required' => 'Tahun pelajaran wajib dipilih',
            'm_mapel_id.required' => 'Mata Pelajaran wajib dipilih',
        ]);

        $kelasIds = $request->kelas_ids;
        $tpId = $request->tp_id;
        $mapelId = $request->m_mapel_id;
        $addedCount = 0;

        foreach ($kelasIds as $kelasId) {
            // Check duplicate
            $exists = KelasAjar::where('guru_id', $guruId)
                ->where('kelas_id', $kelasId)
                ->where('tp_id', $tpId)
                ->where('m_mapel_id', $mapelId)
                ->exists();

            if (!$exists) {
                KelasAjar::create([
                    'guru_id' => $guruId,
                    'kelas_id' => $kelasId,
                    'tp_id' => $tpId,
                    'm_mapel_id' => $mapelId,
                    'is_active' => true,
                ]);
                $addedCount++;
            }
        }

        if ($addedCount === 0) {
            return redirect()->back()->with('error', 'Semua kelas yang dipilih sudah ada di daftar ajar anda untuk mapel tersebut.');
        }

        return redirect()->route('admin.guru.kelas-ajar.index')
            ->with('success', "Berhasil menambahkan $addedCount kelas ajar.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $kelasAjar = KelasAjar::findOrFail($id);

        // Security check: ensure this record belongs to the logged-in guru
        if ($kelasAjar->guru_id !== $guru->id) {
            return redirect()->back()->with('error', 'Anda tidak berhak menghapus data ini.');
        }

        $kelasAjar->delete();

        return redirect()->route('admin.guru.kelas-ajar.index')
            ->with('success', 'Kelas ajar berhasil dihapus.');
    }

    /**
     * Store a newly created mapel assignment (GuruAjar).
     */
    public function storeMapel(Request $request)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $guru = Guru::where('user_id', $userId)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $request->validate([
            'mapel_ids' => 'required|array|min:1',
            'mapel_ids.*' => 'exists:m_mapels,id',
        ], [
            'mapel_ids.required' => 'Minimal satu mapel harus dipilih',
        ]);

        $mapelIds = $request->mapel_ids;
        $addedCount = 0;
        $skippedCount = 0;

        foreach ($mapelIds as $mapelId) {
            // Check duplicate
            $exists = GuruAjar::where('guru_id', $guru->id)
                ->where('mapel_id', $mapelId)
                ->exists();

            if (!$exists) {
                GuruAjar::create([
                    'guru_id' => $guru->id,
                    'mapel_id' => $mapelId,
                    'is_active' => true,
                ]);
                $addedCount++;
            } else {
                $skippedCount++;
            }
        }

        if ($addedCount > 0) {
            $message = "Berhasil menambahkan {$addedCount} mapel.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} mapel sudah ada sebelumnya.";
            }
            return redirect()->route('admin.guru.kelas-ajar.index')
                ->with('success', $message);
        } else {
            return redirect()->route('admin.guru.kelas-ajar.index')
                ->with('error', 'Semua mapel yang dipilih sudah ada.');
        }
    }
}
