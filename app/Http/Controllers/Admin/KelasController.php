<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10);

        if ($perPage === 'all') {
            $kelas = Kelas::orderBy('nm_kls')->get();
            // Wrap in a custom paginator for view compatibility
            $kelas = new \Illuminate\Pagination\LengthAwarePaginator(
                $kelas,
                $kelas->count(),
                $kelas->count(),
                1
            );
        } else {
            $kelas = Kelas::orderBy('nm_kls')->paginate((int) $perPage);
        }

        return view('admin.kelas.index', compact('kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.kelas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nm_kls' => 'required|string|max:15|unique:kelas,nm_kls',
            'alias' => 'required|string|max:6',
        ], [
            'nm_kls.required' => 'Nama kelas wajib diisi',
            'nm_kls.max' => 'Nama kelas maksimal 15 karakter',
            'nm_kls.unique' => 'Nama kelas sudah ada',
            'alias.required' => 'Alias wajib diisi',
            'alias.max' => 'Alias maksimal 6 karakter',
        ]);

        Kelas::create($request->only(['nm_kls', 'alias']));

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kela)
    {
        return view('admin.kelas.show', ['kelas' => $kela]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kela)
    {
        return view('admin.kelas.edit', ['kelas' => $kela]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nm_kls' => 'required|string|max:15|unique:kelas,nm_kls,' . $kela->id,
            'alias' => 'required|string|max:6',
        ], [
            'nm_kls.required' => 'Nama kelas wajib diisi',
            'nm_kls.max' => 'Nama kelas maksimal 15 karakter',
            'nm_kls.unique' => 'Nama kelas sudah ada',
            'alias.required' => 'Alias wajib diisi',
            'alias.max' => 'Alias maksimal 6 karakter',
        ]);

        $kela->update($request->only(['nm_kls', 'alias']));

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kela)
    {
        $kela->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus!');
    }
}
