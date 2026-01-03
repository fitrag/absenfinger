<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash;

class MUser extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'foto',
        'username',
        'password',
        'level',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get the roles associated with the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * Get the student associated with the user.
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    /**
     * Get all sessions for the user.
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class, 'user_id');
    }

    /**
     * Get the latest session for the user.
     */
    public function latestSession()
    {
        return $this->hasOne(UserSession::class, 'user_id')->latestOfMany('logged_in_at');
    }

    /**
     * Get activity logs for the user.
     */
    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class, 'user_id');
    }

    /**
     * Check if user is currently online.
     */
    public function getIsOnlineAttribute()
    {
        $session = $this->latestSession;

        return $session &&
            $session->is_online &&
            $session->last_activity &&
            $session->last_activity->diffInMinutes(now()) < 5;
    }
}
