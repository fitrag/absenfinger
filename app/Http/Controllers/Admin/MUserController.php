<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MUser;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MUserController extends Controller
{
    /**
     * Display a listing of the resource (Guru only).
     */
    public function guru(Request $request)
    {
        $perPage = $request->get('perPage', 10);
        $search = $request->get('search');

        $query = MUser::with('roles')->where('level', 'guru');

        // Server-side search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('roles', function ($roleQuery) use ($search) {
                        $roleQuery->where('nama_role', 'like', "%{$search}%");
                    });
            });
        }

        $query->orderBy('name');

        if ($perPage === 'all') {
            $users = $query->get();
            $users = new \Illuminate\Pagination\LengthAwarePaginator(
                $users,
                $users->count(),
                $users->count(),
                1
            );
        } else {
            $users = $query->paginate((int) $perPage)->appends($request->query());
        }

        $roles = Role::active()->orderBy('nama_role')->get();

        if ($request->ajax()) {
            return view('admin.users.partials.guru_table', compact('users'))->render();
        }

        return view('admin.users.guru_index', compact('users', 'roles'));
    }

    /**
     * Display a listing of the resource (Siswa only).
     */
    public function siswa(Request $request)
    {
        $perPage = $request->get('perPage', 10);
        $search = $request->get('search');

        $query = MUser::with('roles')->where('level', 'siswa');

        // Server-side search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('roles', function ($roleQuery) use ($search) {
                        $roleQuery->where('nama_role', 'like', "%{$search}%");
                    });
            });
        }

        $query->orderBy('name');

        if ($perPage === 'all') {
            $users = $query->get();
            $users = new \Illuminate\Pagination\LengthAwarePaginator(
                $users,
                $users->count(),
                $users->count(),
                1
            );
        } else {
            $users = $query->paginate((int) $perPage)->appends($request->query());
        }

        $roles = Role::active()->orderBy('nama_role')->get();

        if ($request->ajax()) {
            return view('admin.users.partials.siswa_table', compact('users'))->render();
        }

        return view('admin.users.siswa_index', compact('users', 'roles'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10);
        $search = $request->get('search');
        $level = $request->get('level');

        $query = MUser::with('roles');

        if ($level) {
            $query->where('level', $level);
        }

        // Server-side search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('roles', function ($roleQuery) use ($search) {
                        $roleQuery->where('nama_role', 'like', "%{$search}%");
                    });
            });
        }

        $query->orderBy('name');

        if ($perPage === 'all') {
            $users = $query->get();
            $users = new \Illuminate\Pagination\LengthAwarePaginator(
                $users,
                $users->count(),
                $users->count(),
                1
            );
        } else {
            $users = $query->paginate((int) $perPage)->appends($request->query());
        }

        $roles = Role::active()->orderBy('nama_role')->get();

        if ($request->ajax()) {
            return view('admin.users.partials.table', compact('users'))->render();
        }

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::active()->orderBy('nama_role')->get();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:m_roles,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Nama wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'roles.*.exists' => 'Role tidak valid',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ]);

        $data = $request->only(['name', 'username', 'password']);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('users', 'public');
        }

        // Handle boolean fields
        $data['is_active'] = $request->has('is_active') ? true : null;

        $user = MUser::create($data);

        // Sync roles
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MUser $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MUser $user)
    {
        $user->load('roles');
        $roles = Role::active()->orderBy('nama_role')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MUser $user)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'roles' => 'nullable|array',
            'roles.*' => 'exists:m_roles,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $request->validate($rules, [
            'name.required' => 'Nama wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.min' => 'Password minimal 6 karakter',
            'roles.*.exists' => 'Role tidak valid',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ]);

        $data = $request->only(['name', 'username']);

        // Update password only if provided
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('users', 'public');
        }

        // Handle boolean fields
        $data['is_active'] = $request->has('is_active') ? true : null;

        $user->update($data);

        // Sync roles
        $user->roles()->sync($request->roles ?? []);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MUser $user)
    {
        // Delete foto if exists
        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}
