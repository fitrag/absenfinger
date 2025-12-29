<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10);

        if ($perPage === 'all') {
            $jurusan = Jurusan::orderBy('bidang')->get();
            $jurusan = new \Illuminate\Pagination\LengthAwarePaginator(
                $jurusan,
                $jurusan->count(),
                $jurusan->count(),
                1
            );
        } else {
            $jurusan = Jurusan::orderBy('bidang')->paginate((int) $perPage);
        }

        return view('admin.jurusan.index', compact('jurusan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.jurusan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bidang' => 'required|string|max:100',
            'program' => 'required|string|max:100',
            'paket_keahlian' => 'required|string|max:100',
        ], [
            'bidang.required' => 'Bidang wajib diisi',
            'bidang.max' => 'Bidang maksimal 100 karakter',
            'program.required' => 'Program wajib diisi',
            'program.max' => 'Program maksimal 100 karakter',
            'paket_keahlian.required' => 'Paket Keahlian wajib diisi',
            'paket_keahlian.max' => 'Paket Keahlian maksimal 100 karakter',
        ]);

        Jurusan::create($request->only(['bidang', 'program', 'paket_keahlian']));

        return redirect()->route('admin.jurusan.index')
            ->with('success', 'Jurusan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jurusan $jurusan)
    {
        return view('admin.jurusan.show', compact('jurusan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jurusan $jurusan)
    {
        return view('admin.jurusan.edit', compact('jurusan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'bidang' => 'required|string|max:100',
            'program' => 'required|string|max:100',
            'paket_keahlian' => 'required|string|max:100',
        ], [
            'bidang.required' => 'Bidang wajib diisi',
            'bidang.max' => 'Bidang maksimal 100 karakter',
            'program.required' => 'Program wajib diisi',
            'program.max' => 'Program maksimal 100 karakter',
            'paket_keahlian.required' => 'Paket Keahlian wajib diisi',
            'paket_keahlian.max' => 'Paket Keahlian maksimal 100 karakter',
        ]);

        $jurusan->update($request->only(['bidang', 'program', 'paket_keahlian']));

        return redirect()->route('admin.jurusan.index')
            ->with('success', 'Jurusan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();

        return redirect()->route('admin.jurusan.index')
            ->with('success', 'Jurusan berhasil dihapus!');
    }
}
