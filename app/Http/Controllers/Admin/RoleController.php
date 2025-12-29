<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_role', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $perPage = $request->get('perPage', 10);
        $roles = $query->orderBy('nama_role')->paginate($perPage)->withQueryString();

        return view('admin.role.index', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_role' => 'required|string|max:255|unique:m_roles,nama_role',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Role::create($validated);

        return redirect()->route('admin.role.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'nama_role' => ['required', 'string', 'max:255', Rule::unique('m_roles', 'nama_role')->ignore($role->id)],
            'keterangan' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $role->update($validated);

        return redirect()->route('admin.role.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('admin.role.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}
