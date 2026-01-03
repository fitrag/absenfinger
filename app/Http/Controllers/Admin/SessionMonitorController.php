<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Models\UserActivityLog;
use App\Models\MUser;
use Illuminate\Http\Request;

class SessionMonitorController extends Controller
{
    /**
     * Display session monitoring dashboard.
     */
    public function index(Request $request)
    {
        $statusFilter = $request->get('status');
        $search = $request->get('search');
        $kelasId = $request->get('kelas_id');
        $perPage = $request->get('per_page', 36);
        $activeTab = $request->get('tab', 'staff'); // 'staff' or 'siswa'

        // Helper function to filter by online status
        $filterByStatus = function ($users, $status) {
            if ($status === 'online') {
                return $users->filter(function ($user) {
                    return $user->latestSession &&
                        $user->latestSession->is_online &&
                        $user->latestSession->last_activity &&
                        $user->latestSession->last_activity->diffInMinutes(now()) < 5;
                });
            } elseif ($status === 'offline') {
                return $users->filter(function ($user) {
                    return !$user->latestSession ||
                        !$user->latestSession->is_online ||
                        !$user->latestSession->last_activity ||
                        $user->latestSession->last_activity->diffInMinutes(now()) >= 5;
                });
            }
            return $users;
        };

        // Base query with search
        $baseQuery = MUser::with(['latestSession'])->orderBy('name');

        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Get Staff (admin + guru)
        $staffQuery = clone $baseQuery;
        $staffUsers = $staffQuery->whereIn('level', ['admin', 'guru'])->get();
        $staffUsers = $filterByStatus($staffUsers, $statusFilter);

        // Get Siswa with kelas - sorted by kelas name then user name
        $siswaQuery = MUser::with(['latestSession', 'student.kelas'])
            ->where('level', 'siswa')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->leftJoin('kelas', 'students.kelas_id', '=', 'kelas.id')
            ->orderBy('kelas.nm_kls', 'asc')
            ->orderBy('users.name', 'asc')
            ->select('users.*');

        // Apply search for siswa
        if ($search) {
            $siswaQuery->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.username', 'like', "%{$search}%");
            });
        }

        // Filter by kelas
        if ($kelasId) {
            $siswaQuery->where('students.kelas_id', $kelasId);
        }

        // Apply status filter before pagination
        if ($statusFilter === 'online') {
            $siswaQuery->whereHas('latestSession', function ($q) {
                $q->where('is_online', true)
                    ->where('last_activity', '>=', now()->subMinutes(5));
            });
        } elseif ($statusFilter === 'offline') {
            $siswaQuery->where(function ($q) {
                $q->whereDoesntHave('latestSession')
                    ->orWhereHas('latestSession', function ($subQ) {
                        $subQ->where('is_online', false)
                            ->orWhere('last_activity', '<', now()->subMinutes(5));
                    });
            });
        }

        // Paginate siswa
        $siswaUsers = $siswaQuery->paginate($perPage)->withQueryString();

        // Get kelas list for filter dropdown
        $kelasList = \App\Models\Kelas::orderBy('nm_kls')->get();

        // Stats
        $staffOnline = UserSession::where('is_online', true)
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->whereHas('user', fn($q) => $q->whereIn('level', ['admin', 'guru']))
            ->count();

        $siswaOnline = UserSession::where('is_online', true)
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->whereHas('user', fn($q) => $q->where('level', 'siswa'))
            ->count();

        $stats = [
            'totalUsers' => MUser::count(),
            'totalStaff' => MUser::whereIn('level', ['admin', 'guru'])->count(),
            'totalSiswa' => MUser::where('level', 'siswa')->count(),
            'onlineNow' => $staffOnline + $siswaOnline,
            'staffOnline' => $staffOnline,
            'siswaOnline' => $siswaOnline,
            'todayLogins' => UserSession::whereDate('logged_in_at', today())->count(),
        ];

        return view('admin.sessions.index', compact(
            'staffUsers',
            'siswaUsers',
            'stats',
            'statusFilter',
            'search',
            'activeTab',
            'kelasList',
            'kelasId',
            'perPage'
        ));
    }

    /**
     * Get online users for AJAX polling.
     */
    public function getOnlineUsers()
    {
        $onlineSessions = UserSession::with('user')
            ->where('is_online', true)
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->orderBy('last_activity', 'desc')
            ->get();

        return response()->json([
            'count' => $onlineSessions->count(),
            'users' => $onlineSessions->map(function ($session) {
                return [
                    'id' => $session->user_id,
                    'name' => $session->user->name ?? 'Unknown',
                    'level' => $session->user->level ?? '-',
                    'device' => $session->device_name,
                    'location' => $session->location,
                    'last_activity' => $session->last_activity->diffForHumans(),
                ];
            }),
        ]);
    }

    /**
     * View user's activity log.
     */
    public function getUserActivities(Request $request, $userId)
    {
        $user = MUser::findOrFail($userId);
        $date = $request->get('date', today()->format('Y-m-d'));

        $activities = UserActivityLog::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $sessions = UserSession::where('user_id', $userId)
            ->orderBy('logged_in_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.sessions.activities', compact('user', 'activities', 'sessions', 'date'));
    }

    /**
     * Force logout a session.
     */
    public function forceLogout(Request $request, $sessionId)
    {
        $session = UserSession::findOrFail($sessionId);

        // Log the forced logout
        UserActivityLog::logLogout($session->user_id, $session->id, $request->ip(), true);

        // Mark session as logged out
        $session->markAsLoggedOut();

        return redirect()->back()->with('success', 'Sesi berhasil di-logout paksa.');
    }

    /**
     * Get all active sessions list.
     */
    public function activeSessions()
    {
        $sessions = UserSession::with('user')
            ->whereNull('logged_out_at')
            ->orderBy('last_activity', 'desc')
            ->paginate(20);

        return view('admin.sessions.active', compact('sessions'));
    }
}
