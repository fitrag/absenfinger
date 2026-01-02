<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Guru;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = User::with(['guru', 'student.kelas', 'student.jurusan'])->findOrFail($userId);

        return view('admin.profile.index', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->username = $validated['username'];

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto if exists
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $fotoPath = $request->file('foto')->store('profile-photos', 'public');
            $user->foto = $fotoPath;
        }

        $user->save();

        // Update session data
        Session::put('user_name', $user->name);
        Session::put('user_foto', $user->foto);

        return redirect()->back()->with('success', 'Profile berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak valid.']);
        }

        $user->password = $validated['password'];
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Update guru personal data.
     */
    public function updateGuru(Request $request)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = User::findOrFail($userId);

        // Check if user is guru
        if ($user->level !== 'guru') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'nip' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
            'nama' => 'required|string|max:255',
            'tmpt_lhr' => 'nullable|string|max:100',
            'tgl_lhr' => 'nullable|date',
            'jen_kel' => 'nullable|in:L,P',
            'no_tlp' => 'nullable|string|max:20',
        ]);

        // Find or create guru record
        $guru = Guru::where('user_id', $userId)->first();

        if ($guru) {
            $guru->update($validated);
        } else {
            $validated['user_id'] = $userId;
            $validated['username'] = $user->username;
            Guru::create($validated);
        }

        // Also update user name
        $user->name = $validated['nama'];
        $user->save();
        Session::put('user_name', $user->name);

        return redirect()->back()->with('success', 'Data pribadi berhasil diperbarui.');
    }

    /**
     * Update student personal data.
     */
    public function updateStudent(Request $request)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = User::findOrFail($userId);

        // Check if user is siswa
        if ($user->level !== 'siswa') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'nullable|string|max:20',
            'tmpt_lhr' => 'nullable|string|max:100',
            'tgl_lhr' => 'nullable|date',
            'jen_kel' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:50',
            'almt_siswa' => 'nullable|string',
            'no_tlp' => 'nullable|string|max:20',
            'nm_ayah' => 'nullable|string|max:100',
        ]);

        // Find student record
        $student = Student::where('user_id', $userId)->first();

        if ($student) {
            $student->update($validated);
        }

        // Also update user name
        $user->name = $validated['name'];
        $user->save();
        Session::put('user_name', $user->name);

        return redirect()->back()->with('success', 'Data pribadi berhasil diperbarui.');
    }
}
