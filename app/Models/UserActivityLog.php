<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'action',
        'url',
        'method',
        'description',
        'ip_address',
    ];

    /**
     * Get the user that owns the activity.
     */
    public function user()
    {
        return $this->belongsTo(MUser::class, 'user_id');
    }

    /**
     * Get the session that this activity belongs to.
     */
    public function session()
    {
        return $this->belongsTo(UserSession::class, 'session_id');
    }

    /**
     * Scope to filter by action type.
     */
    public function scopeOfAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Log a page view activity.
     */
    public static function logPageView($userId, $sessionId, $url, $method = 'GET', $ip = null)
    {
        return static::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'action' => 'page_view',
            'url' => $url,
            'method' => $method,
            'description' => 'Mengakses halaman: ' . $url,
            'ip_address' => $ip,
        ]);
    }

    /**
     * Log a login activity.
     */
    public static function logLogin($userId, $sessionId, $ip = null, $deviceName = null)
    {
        return static::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'action' => 'login',
            'description' => 'Login dari perangkat: ' . ($deviceName ?? 'Unknown'),
            'ip_address' => $ip,
        ]);
    }

    /**
     * Log a logout activity.
     */
    public static function logLogout($userId, $sessionId = null, $ip = null, $forced = false)
    {
        return static::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'action' => 'logout',
            'description' => $forced ? 'Logout paksa oleh admin' : 'Logout manual',
            'ip_address' => $ip,
        ]);
    }

    /**
     * Log session kicked (another device login).
     */
    public static function logSessionKicked($userId, $sessionId, $ip = null)
    {
        return static::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'action' => 'session_kicked',
            'description' => 'Sesi diakhiri karena login dari perangkat lain',
            'ip_address' => $ip,
        ]);
    }
}
