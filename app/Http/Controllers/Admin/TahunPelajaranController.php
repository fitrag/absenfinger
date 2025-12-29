<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class TahunPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = TahunPelajaran::query();

        if ($search) {
            $query->where('nm_tp', 'like', "%{$search}%");
        }

        $tahunPelajarans = $query->orderBy('nm_tp', 'desc')->get();

        return view('admin.tp.index', compact('tahunPelajarans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nm_tp' => 'required|string|max:20|unique:m_tp,nm_tp',
        ], [
            'nm_tp.required' => 'Tahun Pelajaran wajib diisi',
            'nm_tp.max' => 'Maksimal 20 karakter',
            'nm_tp.unique' => 'Tahun Pelajaran sudah ada',
        ]);

        TahunPelajaran::create([
            'nm_tp' => $request->nm_tp,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.tp.index')
            ->with('success', 'Tahun Pelajaran berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunPelajaran $tp)
    {
        $request->validate([
            'nm_tp' => 'required|string|max:20|unique:m_tp,nm_tp,' . $tp->id,
        ], [
            'nm_tp.required' => 'Tahun Pelajaran wajib diisi',
            'nm_tp.max' => 'Maksimal 20 karakter',
            'nm_tp.unique' => 'Tahun Pelajaran sudah ada',
        ]);

        $tp->update([
            'nm_tp' => $request->nm_tp,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.tp.index')
            ->with('success', 'Tahun Pelajaran berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunPelajaran $tp)
    {
        $tp->delete();

        return redirect()->route('admin.tp.index')
            ->with('success', 'Tahun Pelajaran berhasil dihapus!');
    }
}
