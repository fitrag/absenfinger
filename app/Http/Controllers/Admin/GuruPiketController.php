<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruAbsence;
use App\Models\Kelas;
use Illuminate\Http\Request;

class GuruPiketController extends Controller
{
    public function ketidakhadiran(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $search = $request->input('search');

        // Query absences for the date
        $query = GuruAbsence::with(['guru'])
            ->whereDate('date', $date);

        if ($search) {
            $query->whereHas('guru', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $absences = $query->orderBy('created_at', 'desc')->get();

        // Stats
        $stats = [
            'sakit' => $absences->where('status', 'sakit')->count(),
            'izin' => $absences->where('status', 'izin')->count(),
            'alpha' => $absences->where('status', 'alpha')->count(),
            'total' => $absences->count(),
        ];

        // Get list of gurus and kelas for dropdowns
        $gurus = Guru::orderBy('nama')->get();
        $kelasList = Kelas::orderBy('nm_kls')->get();

        return view('admin.guru_piket.ketidakhadiran', compact('absences', 'date', 'stats', 'gurus', 'kelasList'));
    }

    public function storeKetidakhadiran(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:m_gurus,id',
            'date' => 'required|date',
            'status' => 'required|string|in:sakit,izin,alpha',
            'kelas_ids' => 'nullable|array',
            'kelas_ids.*' => 'exists:kelas,id',
            'jam_ke' => 'nullable|string',
            'ket' => 'nullable|string',
        ]);

        GuruAbsence::create([
            'guru_id' => $request->guru_id,
            'date' => $request->date,
            'status' => $request->status,
            'kelas_ids' => $request->kelas_ids ?? [],
            'jam_ke' => $request->jam_ke,
            'ket' => $request->ket,
        ]);

        return redirect()->back()->with('success', 'Data ketidakhadiran berhasil disimpan.');
    }

    public function updateKetidakhadiran(Request $request, $id)
    {
        $absence = GuruAbsence::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:sakit,izin,alpha',
            'kelas_ids' => 'nullable|array',
            'kelas_ids.*' => 'exists:kelas,id',
            'jam_ke' => 'nullable|string',
            'ket' => 'nullable|string',
        ]);

        $absence->update([
            'status' => $request->status,
            'kelas_ids' => $request->kelas_ids ?? [],
            'jam_ke' => $request->jam_ke,
            'ket' => $request->ket,
        ]);

        return redirect()->back()->with('success', 'Data ketidakhadiran berhasil diperbarui.');
    }

    public function destroyKetidakhadiran($id)
    {
        $absence = GuruAbsence::findOrFail($id);
        $absence->delete();

        return redirect()->back()->with('success', 'Data ketidakhadiran berhasil dihapus.');
    }
}
