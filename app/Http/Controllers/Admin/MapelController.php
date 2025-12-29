<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10);
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Mapel::query();

        // Server-side search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nm_mapel', 'like', "%{$search}%")
                    ->orWhere('alias', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $query->orderBy('nm_mapel');

        if ($perPage === 'all') {
            $mapels = $query->get();
            $mapels = new \Illuminate\Pagination\LengthAwarePaginator(
                $mapels,
                $mapels->count(),
                $mapels->count(),
                1
            );
        } else {
            $mapels = $query->paginate((int) $perPage)->appends($request->query());
        }

        return view('admin.mapel.index', compact('mapels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nm_mapel' => 'required|string|max:100',
            'alias' => 'nullable|string|max:20',
        ], [
            'nm_mapel.required' => 'Nama mapel wajib diisi',
            'nm_mapel.max' => 'Nama mapel maksimal 100 karakter',
            'alias.max' => 'Alias maksimal 20 karakter',
        ]);

        $data = $request->only(['nm_mapel', 'alias']);
        $data['is_active'] = $request->has('is_active');

        Mapel::create($data);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mapel berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mapel $mapel)
    {
        $request->validate([
            'nm_mapel' => 'required|string|max:100',
            'alias' => 'nullable|string|max:20',
        ], [
            'nm_mapel.required' => 'Nama mapel wajib diisi',
            'nm_mapel.max' => 'Nama mapel maksimal 100 karakter',
            'alias.max' => 'Alias maksimal 20 karakter',
        ]);

        $data = $request->only(['nm_mapel', 'alias']);
        $data['is_active'] = $request->has('is_active');

        $mapel->update($data);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mapel berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mapel $mapel)
    {
        $mapel->delete();

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mapel berhasil dihapus!');
    }
}
