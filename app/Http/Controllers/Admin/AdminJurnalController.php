<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruJurnal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminJurnalController extends Controller
{
    /**
     * Display a listing of all teachers with their journal status.
     */
    public function index(Request $request)
    {
        $tpId = $request->get('tp_id');
        $search = $request->get('search');

        $activeTp = TahunPelajaran::active()->first();
        $tpList = TahunPelajaran::orderBy('nm_tp', 'desc')->get();

        // Set default tp_id to active TP if not specified
        if (!$tpId && $activeTp) {
            $tpId = $activeTp->id;
        }

        // Get all gurus with their journal counts
        $query = Guru::orderBy('nama');

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $gurus = $query->get();

        // Get journal counts per guru for the selected TP
        $guruJurnalCounts = [];
        foreach ($gurus as $guru) {
            $jurnalQuery = GuruJurnal::where('guru_id', $guru->id);
            if ($tpId) {
                $jurnalQuery->where('tp_id', $tpId);
            }
            $guruJurnalCounts[$guru->id] = $jurnalQuery->count();
        }

        return view('admin.jurnal.index', compact('gurus', 'guruJurnalCounts', 'tpList', 'activeTp', 'tpId', 'search'));
    }

    /**
     * Display journals for a specific guru.
     */
    public function show(Request $request, $guruId)
    {
        $guru = Guru::findOrFail($guruId);

        $tpId = $request->get('tp_id');
        $mapelId = $request->get('mapel_id');
        $kelasId = $request->get('kelas_id');
        $semester = $request->get('semester');

        $activeTp = TahunPelajaran::active()->first();
        $tpList = TahunPelajaran::orderBy('nm_tp', 'desc')->get();

        if (!$tpId && $activeTp) {
            $tpId = $activeTp->id;
        }

        // Get mapels taught by this guru
        $mapelIds = \App\Models\GuruAjar::where('guru_id', $guru->id)
            ->where('is_active', true)
            ->pluck('mapel_id');
        $mapelList = Mapel::whereIn('id', $mapelIds)->orderBy('nm_mapel')->get();

        // Get kelas from this guru's kelas_ajars
        $kelasIds = \App\Models\KelasAjar::where('guru_id', $guru->id)
            ->where('is_active', true)
            ->pluck('kelas_id')
            ->unique();
        $kelasList = Kelas::whereIn('id', $kelasIds)->orderBy('nm_kls')->get();

        // Query jurnals
        $query = GuruJurnal::with(['kelas', 'mapel', 'tp'])
            ->where('guru_id', $guru->id);

        if ($tpId) {
            $query->where('tp_id', $tpId);
        }
        if ($mapelId) {
            $query->where('mapel_id', $mapelId);
        }
        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }
        if ($semester) {
            $query->where('semester', $semester);
        }

        $jurnals = $query->orderByRaw('CAST(tmke AS UNSIGNED) ASC')->get();

        // Group jurnals by mapel, kelas, tp, and semester
        $groupedJurnals = $jurnals->groupBy(function ($jurnal) {
            return $jurnal->mapel_id . '-' . $jurnal->kelas_id . '-' . $jurnal->tp_id . '-' . $jurnal->semester;
        })->map(function ($group) {
            return [
                'mapel' => $group->first()->mapel,
                'kelas' => $group->first()->kelas,
                'tp' => $group->first()->tp,
                'semester' => $group->first()->semester,
                'items' => $group
            ];
        });

        return view('admin.jurnal.show', compact(
            'guru',
            'jurnals',
            'groupedJurnals',
            'tpList',
            'mapelList',
            'kelasList',
            'activeTp',
            'tpId',
            'mapelId',
            'kelasId',
            'semester'
        ));
    }

    /**
     * Download PDF for a specific group of journals.
     */
    public function downloadPdf(Request $request, $guruId)
    {
        $guru = Guru::findOrFail($guruId);

        $mapelId = $request->get('mapel_id');
        $kelasId = $request->get('kelas_id');
        $tpId = $request->get('tp_id');
        $semester = $request->get('semester');

        $mapel = Mapel::find($mapelId);
        $kelas = Kelas::find($kelasId);
        $tp = TahunPelajaran::find($tpId);

        // Query jurnals
        $query = GuruJurnal::with(['kelas', 'mapel', 'tp'])
            ->where('guru_id', $guru->id)
            ->where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId);

        if ($tpId) {
            $query->where('tp_id', $tpId);
        }
        if ($semester) {
            $query->where('semester', $semester);
        }

        $jurnals = $query->orderBy('tanggal', 'asc')->get();

        // Get settings
        $settings = \App\Models\Setting::first();
        $kopImage = \App\Models\Setting::get('kop_image');

        $pdf = Pdf::loadView('admin.guru.jurnal.pdf', [
            'jurnals' => $jurnals,
            'guru' => $guru,
            'mapel' => $mapel,
            'kelas' => $kelas,
            'tp' => $tp,
            'semester' => $semester,
            'settings' => $settings,
            'kopImage' => $kopImage,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $mapelName = $mapel ? $mapel->nm_mapel : 'mapel';
        $kelasName = $kelas ? $kelas->nm_kls : 'kelas';
        $filename = 'jurnal_' . $mapelName . '_' . $kelasName . '_' . date('Y-m-d') . '.pdf';
        $filename = str_replace(' ', '_', $filename);

        return $pdf->stream($filename);
    }
}
