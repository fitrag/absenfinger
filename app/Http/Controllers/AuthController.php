<?php

namespace App\Http\Controllers;

use App\Models\MUser;
use App\Models\Student;
use App\Models\Pkl;
use App\Models\UserSession;
use App\Models\UserActivityLog;
use App\Services\GeoLocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

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

        // Get device info and location
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $deviceName = GeoLocationService::parseDeviceInfo($userAgent);
        $location = GeoLocationService::getLocationFromIp($ipAddress);

        // Invalidate all previous sessions for this user (single device enforcement)
        $oldSessions = UserSession::where('user_id', $user->id)
            ->whereNull('logged_out_at')
            ->get();

        foreach ($oldSessions as $oldSession) {
            // Log that session was kicked
            UserActivityLog::logSessionKicked($user->id, $oldSession->id, $oldSession->ip_address);
            $oldSession->markAsLoggedOut();
        }

        // Create new session token
        $sessionToken = Str::random(64);

        // Create new session record
        $userSession = UserSession::create([
            'user_id' => $user->id,
            'session_token' => $sessionToken,
            'device_name' => $deviceName,
            'ip_address' => $ipAddress,
            'location' => $location,
            'last_activity' => now(),
            'is_online' => true,
            'logged_in_at' => now(),
        ]);

        // Log login activity
        UserActivityLog::logLogin($user->id, $userSession->id, $ipAddress, $deviceName);

        // Store user in session
        Session::put('user_id', $user->id);
        Session::put('user_name', $user->name);
        Session::put('user_username', $user->username);
        Session::put('user_foto', $user->foto);
        Session::put('user_level', $user->level);
        Session::put('user_roles', $user->roles->pluck('nama_role')->toArray());
        Session::put('session_token', $sessionToken); // For session validation

        // Redirect to admin dashboard
        return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, ' . $user->name);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        $userId = Session::get('user_id');
        $sessionToken = Session::get('session_token');

        if ($userId && $sessionToken) {
            // Find and mark session as logged out
            $userSession = UserSession::where('user_id', $userId)
                ->where('session_token', $sessionToken)
                ->first();

            if ($userSession) {
                // Log logout activity
                UserActivityLog::logLogout($userId, $userSession->id, $request->ip());
                $userSession->markAsLoggedOut();
            }
        }

        Session::flush();

        return redirect()->route('login')->with('success', 'Anda berhasil logout');
    }
}
