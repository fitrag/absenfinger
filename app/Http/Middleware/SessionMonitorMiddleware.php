<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\UserSession;
use App\Models\UserActivityLog;
use Symfony\Component\HttpFoundation\Response;

class SessionMonitorMiddleware
{
    /**
     * Routes to exclude from activity logging (to reduce noise).
     */
    protected $excludedRoutes = [
        'admin/sessions/online', // AJAX polling
        '_debugbar',
        'livewire',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = Session::get('user_id');
        $sessionToken = Session::get('session_token');

        // Skip if not logged in
        if (!$userId || !$sessionToken) {
            return $next($request);
        }

        // Validate session token (enforce single device login)
        $currentSession = UserSession::where('session_token', $sessionToken)
            ->where('user_id', $userId)
            ->whereNull('logged_out_at')
            ->first();

        if (!$currentSession) {
            // Session was invalidated (user logged in elsewhere)
            Session::flush();

            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir karena login dari perangkat lain.');
        }

        // Update last activity
        $currentSession->updateActivity();

        // Log page view (exclude certain routes to reduce noise)
        if (!$this->shouldExclude($request)) {
            $this->logActivity($request, $userId, $currentSession->id);
        }

        return $next($request);
    }

    /**
     * Check if request should be excluded from logging.
     */
    protected function shouldExclude(Request $request): bool
    {
        $path = $request->path();

        foreach ($this->excludedRoutes as $excluded) {
            if (str_contains($path, $excluded)) {
                return true;
            }
        }

        // Exclude AJAX requests for polling
        if ($request->ajax() && $request->isMethod('GET')) {
            return true;
        }

        return false;
    }

    /**
     * Log user activity.
     */
    protected function logActivity(Request $request, $userId, $sessionId): void
    {
        // Only log page views, not every request
        if (!$request->isMethod('GET')) {
            return;
        }

        // Throttle logging - max 1 log per URL per minute
        $cacheKey = "activity_log_{$userId}_{$sessionId}_" . md5($request->path());

        if (cache()->has($cacheKey)) {
            return;
        }

        cache()->put($cacheKey, true, 60); // 1 minute throttle

        UserActivityLog::logPageView(
            $userId,
            $sessionId,
            $request->path(),
            $request->method(),
            $request->ip()
        );
    }
}
