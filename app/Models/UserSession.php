<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_token',
        'device_name',
        'ip_address',
        'location',
        'last_activity',
        'is_online',
        'logged_in_at',
        'logged_out_at',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'last_activity' => 'datetime',
        'logged_in_at' => 'datetime',
        'logged_out_at' => 'datetime',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user()
    {
        return $this->belongsTo(MUser::class, 'user_id');
    }

    /**
     * Get the activity logs for this session.
     */
    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class, 'session_id');
    }

    /**
     * Scope to get online sessions.
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true)
            ->where('last_activity', '>=', now()->subMinutes(5));
    }

    /**
     * Scope to get active sessions (not logged out).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('logged_out_at');
    }

    /**
     * Check if session is currently online (active within 5 minutes).
     */
    public function getIsCurrentlyOnlineAttribute()
    {
        return $this->is_online &&
            $this->last_activity &&
            $this->last_activity->diffInMinutes(now()) < 5;
    }

    /**
     * Mark session as offline/logged out.
     */
    public function markAsLoggedOut()
    {
        $this->update([
            'is_online' => false,
            'logged_out_at' => now(),
        ]);
    }

    /**
     * Update last activity.
     */
    public function updateActivity()
    {
        $this->update([
            'last_activity' => now(),
            'is_online' => true,
        ]);
    }
}
