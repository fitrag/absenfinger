<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuruAjar;
use App\Models\KelasAjar;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class GuruAjarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $activeTp = TahunPelajaran::active()->first();
        $selectedTpId = $request->get('tp', $activeTp ? $activeTp->id : null);

        $query = GuruAjar::with(['guru', 'mapel']);

        // Server-side search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('guru', function ($guruQuery) use ($search) {
                    $guruQuery->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                })
                    ->orWhereHas('mapel', function ($mapelQuery) use ($search) {
                        $mapelQuery->where('nm_mapel', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        // Get all and group by guru
        $allData = $query->get();
        $groupedData = $allData->groupBy('guru_id')->map(function ($items) {
            return [
                'guru' => $items->first()->guru,
                'mapels' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'mapel' => $item->mapel,
                        'is_active' => $item->is_active,
                    ];
                }),
            ];
        });

        $gurus = Guru::orderBy('nama')->get();
        $mapels = Mapel::active()->orderBy('nm_mapel')->get();
        $kelasList = Kelas::orderBy('nm_kls')->get();
        $tpList = TahunPelajaran::orderBy('nm_tp', 'desc')->get();

        // Get kelas ajar data grouped by guru then by mapel
        $kelasAjarData = KelasAjar::with(['guru', 'kelas', 'tp', 'mapel'])
            ->when($selectedTpId, function ($q) use ($selectedTpId) {
                $q->where('tp_id', $selectedTpId);
            })
            ->get()
            ->groupBy('guru_id')
            ->map(function ($guruGroup) {
                return $guruGroup->groupBy('m_mapel_id');
            });

        return view('admin.guruajar.index', compact(
            'groupedData',
            'gurus',
            'mapels',
            'kelasList',
            'tpList',
            'activeTp',
            'kelasAjarData',
            'selectedTpId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:m_gurus,id',
            'mapels' => 'required|array|min:1',
            'mapels.*' => 'exists:m_mapels,id',
        ], [
            'guru_id.required' => 'Guru wajib dipilih',
            'guru_id.exists' => 'Guru tidak valid',
            'mapels.required' => 'Minimal pilih 1 mapel',
            'mapels.min' => 'Minimal pilih 1 mapel',
            'mapels.*.exists' => 'Mapel tidak valid',
        ]);

        $guruId = $request->guru_id;
        $mapelIds = $request->mapels;
        $isActive = $request->has('is_active');
        $added = 0;
        $skipped = 0;

        foreach ($mapelIds as $mapelId) {
            $exists = GuruAjar::where('guru_id', $guruId)
                ->where('mapel_id', $mapelId)
                ->exists();
            if (!$exists) {
                GuruAjar::create([
                    'guru_id' => $guruId,
                    'mapel_id' => $mapelId,
                    'is_active' => $isActive,
                ]);
                $added++;
            } else {
                $skipped++;
            }
        }

        $message = "Berhasil menambahkan {$added} data guru mengajar.";
        if ($skipped > 0) {
            $message .= " {$skipped} data sudah ada sebelumnya.";
        }

        return redirect()->route('admin.guruajar.index')
            ->with('success', $message);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GuruAjar $guruajar)
    {
        $request->validate([
            'guru_id' => 'required|exists:m_gurus,id',
            'mapel_id' => 'required|exists:m_mapels,id',
        ], [
            'guru_id.required' => 'Guru wajib dipilih',
            'guru_id.exists' => 'Guru tidak valid',
            'mapel_id.required' => 'Mapel wajib dipilih',
            'mapel_id.exists' => 'Mapel tidak valid',
        ]);

        // Check duplicate
        $exists = GuruAjar::where('guru_id', $request->guru_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('id', '!=', $guruajar->id)
            ->exists();
        if ($exists) {
            return redirect()->route('admin.guruajar.index')
                ->with('error', 'Kombinasi guru dan mapel sudah ada!');
        }

        $guruajar->update([
            'guru_id' => $request->guru_id,
            'mapel_id' => $request->mapel_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.guruajar.index')
            ->with('success', 'Data guru mengajar berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GuruAjar $guruajar)
    {
        $guruajar->delete();

        return redirect()->route('admin.guruajar.index')
            ->with('success', 'Data guru mengajar berhasil dihapus!');
    }

    /**
     * Store kelas ajar for a guru.
     */
    public function storeKelas(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:m_gurus,id',
            'mapel_id' => 'required|exists:m_mapels,id',
            'kelas' => 'required|array|min:1',
            'kelas.*' => 'exists:kelas,id',
            'tp_id' => 'required|exists:m_tp,id',
        ], [
            'guru_id.required' => 'Guru wajib dipilih',
            'mapel_id.required' => 'Mapel wajib dipilih',
            'kelas.required' => 'Minimal pilih 1 kelas',
            'kelas.min' => 'Minimal pilih 1 kelas',
            'tp_id.required' => 'Tahun pelajaran wajib dipilih',
        ]);

        $guruId = $request->guru_id;
        $mapelId = $request->mapel_id;
        $kelasIds = $request->kelas;
        $tpId = $request->tp_id;
        $isActive = $request->has('is_active');
        $added = 0;
        $skipped = 0;

        foreach ($kelasIds as $kelasId) {
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
                    'is_active' => $isActive,
                ]);
                $added++;
            } else {
                $skipped++;
            }
        }

        $message = "Berhasil menambahkan {$added} kelas ajar.";
        if ($skipped > 0) {
            $message .= " {$skipped} data sudah ada sebelumnya.";
        }

        return redirect()->route('admin.guruajar.index')
            ->with('success', $message);
    }

    /**
     * Remove kelas ajar from storage.
     */
    public function destroyKelas($id)
    {
        $kelasAjar = KelasAjar::findOrFail($id);
        $kelasAjar->delete();

        return redirect()->route('admin.guruajar.index')
            ->with('success', 'Kelas ajar berhasil dihapus!');
    }
}
