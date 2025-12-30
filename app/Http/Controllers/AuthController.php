<?php

namespace App\Http\Controllers;

use App\Models\MUser;
use App\Models\Student;
use App\Models\Pkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Session::has('user_id')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $user = MUser::with('roles')->where('username', $request->username)->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan'])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['username' => 'Akun Anda tidak aktif'])->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password salah'])->withInput();
        }

        // Store user in session
        Session::put('user_id', $user->id);
        Session::put('user_name', $user->name);
        Session::put('user_username', $user->username);
        Session::put('user_foto', $user->foto);
        Session::put('user_level', $user->level);
        Session::put('user_roles', $user->roles->pluck('nama_role')->toArray());

        // Redirect based on user level
        if ($user->level === 'siswa') {
            // Check if student is registered in PKL
            $student = Student::where('user_id', $user->id)->first();
            
            if ($student) {
                $pkl = Pkl::where('student_id', $student->id)->first();
                
                if ($pkl) {
                    return redirect()->route('siswa.pkl.dashboard')->with('success', 'Selamat datang, ' . $user->name);
                }
            }
            
            // Siswa not registered in PKL, redirect to admin dashboard
            return redirect()->route('admin.dashboard')->with('info', 'Anda belum terdaftar dalam program PKL');
        }

        // Redirect to admin dashboard for other levels
        return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, ' . $user->name);
    }

    /**
     * Handle logout request.
     */
    public function logout()
    {
        Session::flush();

        return redirect()->route('login')->with('success', 'Anda berhasil logout');
    }
}
