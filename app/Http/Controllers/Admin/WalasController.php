<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Walas;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class WalasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $selectedTpId = $request->get('tp_id');

        $query = Walas::with(['guru', 'kelas', 'tahunPelajaran']);

        // Filter by tahun pelajaran
        if ($selectedTpId) {
            $query->where('tp_id', $selectedTpId);
        }

        // Server-side search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('guru', function ($guruQuery) use ($search) {
                    $guruQuery->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                })
                    ->orWhereHas('kelas', function ($kelasQuery) use ($search) {
                        $kelasQuery->where('nm_kls', 'like', "%{$search}%");
                    });
            });
        }

        $walasData = $query->orderBy('created_at', 'desc')->get();

        // Get assigned guru and kelas IDs
        $assignedGuruIds = Walas::pluck('guru_id')->toArray();
        $assignedKelasIds = Walas::pluck('kelas_id')->toArray();

        // Get available gurus (not yet assigned as walas)
        $availableGurus = Guru::query()
            ->whereNotIn('id', $assignedGuruIds)
            ->orderBy('nama')
            ->get();

        // Get available kelas (not yet assigned)
        $availableKelas = Kelas::whereNotIn('id', $assignedKelasIds)
            ->orderBy('nm_kls')
            ->get();

        // All gurus and kelas for edit
        $allGurus = Guru::orderBy('nama')->get();
        $allKelas = Kelas::orderBy('nm_kls')->get();

        // Tahun Pelajaran
        $tahunPelajarans = TahunPelajaran::active()->orderBy('nm_tp', 'desc')->get();

        return view('admin.walas.index', compact('walasData', 'availableGurus', 'availableKelas', 'allGurus', 'allKelas', 'tahunPelajarans', 'selectedTpId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:m_gurus,id|unique:m_walas,guru_id',
            'kelas_id' => 'required|exists:kelas,id|unique:m_walas,kelas_id',
            'tp_id' => 'nullable|exists:m_tp,id',
        ], [
            'guru_id.required' => 'Guru wajib dipilih',
            'guru_id.exists' => 'Guru tidak valid',
            'guru_id.unique' => 'Guru sudah menjadi wali kelas',
            'kelas_id.required' => 'Kelas wajib dipilih',
            'kelas_id.exists' => 'Kelas tidak valid',
            'kelas_id.unique' => 'Kelas sudah memiliki wali kelas',
            'tp_id.exists' => 'Tahun Pelajaran tidak valid',
        ]);

        Walas::create([
            'guru_id' => $request->guru_id,
            'kelas_id' => $request->kelas_id,
            'tp_id' => $request->tp_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.walas.index')
            ->with('success', 'Data wali kelas berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Walas $wala)
    {
        $request->validate([
            'guru_id' => 'required|exists:m_gurus,id|unique:m_walas,guru_id,' . $wala->id,
            'kelas_id' => 'required|exists:kelas,id|unique:m_walas,kelas_id,' . $wala->id,
            'tp_id' => 'nullable|exists:m_tp,id',
        ], [
            'guru_id.required' => 'Guru wajib dipilih',
            'guru_id.exists' => 'Guru tidak valid',
            'guru_id.unique' => 'Guru sudah menjadi wali kelas',
            'kelas_id.required' => 'Kelas wajib dipilih',
            'kelas_id.exists' => 'Kelas tidak valid',
            'kelas_id.unique' => 'Kelas sudah memiliki wali kelas',
            'tp_id.exists' => 'Tahun Pelajaran tidak valid',
        ]);

        $wala->update([
            'guru_id' => $request->guru_id,
            'kelas_id' => $request->kelas_id,
            'tp_id' => $request->tp_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.walas.index')
            ->with('success', 'Data wali kelas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Walas $wala)
    {
        $wala->delete();

        return redirect()->route('admin.walas.index')
            ->with('success', 'Data wali kelas berhasil dihapus!');
    }
}
