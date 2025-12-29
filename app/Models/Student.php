<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'finger_id',
        'nis',
        'nisn',
        'name',
        'tmpt_lhr',
        'tgl_lhr',
        'jen_kel',
        'agama',
        'almt_siswa',
        'no_tlp',
        'nm_ayah',
        'kelas_id',
        'm_jurusan_id',
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tgl_lhr' => 'date',
    ];

    /**
     * Get the kelas that the student belongs to.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Get the jurusan that the student belongs to.
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'm_jurusan_id');
    }

    /**
     * Get the user associated with the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(MUser::class, 'user_id');
    }

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
