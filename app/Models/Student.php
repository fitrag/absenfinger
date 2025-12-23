<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'finger_id',
        'nis',
        'name',
        'class',
        'major',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get attendance records for the student using NIS as foreign key.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'nis', 'nis');
    }

    /**
     * Scope for active students.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get today's attendance for the student.
     */
    public function todayAttendance()
    {
        return $this->attendances()
            ->whereDate('checktime', today())
            ->orderBy('checktime', 'asc');
    }

    /**
     * Get today's check-in record.
     */
    public function todayCheckIn()
    {
        return $this->attendances()
            ->whereDate('checktime', today())
            ->where('checktype', 0)
            ->first();
    }

    /**
     * Get today's check-out record.
     */
    public function todayCheckOut()
    {
        return $this->attendances()
            ->whereDate('checktime', today())
            ->where('checktype', 1)
            ->first();
    }

    /**
     * Check if student is present today.
     */
    public function isPresentToday(): bool
    {
        return $this->attendances()
            ->whereDate('checktime', today())
            ->where('checktype', 0)
            ->exists();
    }
}
