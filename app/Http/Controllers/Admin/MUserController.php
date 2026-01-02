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
     * Toggle status for guru user (AJAX).
     */
    public function toggleStatusGuru(MUser $user)
    {
        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        // Also update guru record if exists
        if ($user->guru) {
            $user->guru->update(['is_active' => $newStatus]);
        }

        return response()->json([
            'success' => true,
            'is_active' => $newStatus,
            'message' => 'Status berhasil diperbarui'
        ]);
    }

    /**
     * Reset password to username for guru user (AJAX).
     */
    public function resetPasswordToNip(MUser $user)
    {
        // Reset password to username
        $user->update(['password' => $user->username]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset ke Username'
        ]);
    }

    /**
     * Display a listing of the resource (Siswa only).
     */
    public function siswa(Request $request)
    {
        $perPage = $request->get('perPage', 36);
        $search = $request->get('search');
        $kelasId = $request->get('kelas_id');

        $query = MUser::with(['roles', 'student.kelas'])->where('level', 'siswa');

        // Filter by kelas
        if ($kelasId) {
            $query->whereHas('student', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        // Server-side search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('roles', function ($roleQuery) use ($search) {
                        $roleQuery->where('nama_role', 'like', "%{$search}%");
                    })
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('nisn', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    })
                    ->orWhereHas('student.kelas', function ($kelasQuery) use ($search) {
                        $kelasQuery->where('nm_kls', 'like', "%{$search}%");
                    });
            });
        }

        // Order by kelas then by name using join
        $query->select('users.*')
            ->leftJoin('students', 'users.id', '=', 'students.user_id')
            ->leftJoin('kelas', 'students.kelas_id', '=', 'kelas.id')
            ->orderBy('kelas.nm_kls', 'asc')
            ->orderBy('users.name', 'asc');

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
        $kelasList = \App\Models\Kelas::orderBy('nm_kls')->get();

        if ($request->ajax()) {
            return view('admin.users.partials.siswa_table', compact('users'))->render();
        }

        return view('admin.users.siswa_index', compact('users', 'roles', 'kelasList'));
    }

    /**
     * Bulk update status for siswa users.
     */
    public function bulkUpdateStatusSiswa(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|in:active,inactive',
        ], [
            'user_ids.required' => 'Pilih minimal satu siswa',
            'user_ids.min' => 'Pilih minimal satu siswa',
        ]);

        $isActive = $request->status === 'active';
        $updated = MUser::whereIn('id', $request->user_ids)
            ->where('level', 'siswa')
            ->update(['is_active' => $isActive]);

        // Also update student records
        \App\Models\Student::whereIn('user_id', $request->user_ids)
            ->update(['is_active' => $isActive]);

        $statusText = $isActive ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.users.siswa')
            ->with('success', "{$updated} user siswa berhasil {$statusText}");
    }

    /**
     * Toggle status for single user (AJAX).
     */
    public function toggleStatus(MUser $user)
    {
        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        // Also update student record if exists
        if ($user->student) {
            $user->student->update(['is_active' => $newStatus]);
        }

        return response()->json([
            'success' => true,
            'is_active' => $newStatus,
            'message' => 'Status berhasil diperbarui'
        ]);
    }

    /**
     * Reset password to NISN for siswa user (AJAX).
     */
    public function resetPasswordToNisn(MUser $user)
    {
        // Get student data with NISN
        $student = $user->student;

        if (!$student || !$student->nisn) {
            return response()->json([
                'success' => false,
                'message' => 'NISN siswa tidak ditemukan'
            ], 400);
        }

        // Reset password to NISN
        $user->update(['password' => $student->nisn]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset ke NISN'
        ]);
    }

    /**
     * Get users by kelas for AJAX.
     */
    public function getUsersByKelas($kelasId)
    {
        $users = MUser::with(['student.kelas'])
            ->where('level', 'siswa')
            ->whereHas('student', function ($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'is_active']);

        // Add kelas info
        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'is_active' => $user->is_active,
                'kelas' => $user->student->kelas->nm_kls ?? '-',
            ];
        });

        return response()->json($users);
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
