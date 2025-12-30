<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'checktime',
        'checktype',
        'dudi_id',
        'latitude',
        'longitude',
        'is_pkl',
    ];

    protected $casts = [
        'checktime' => 'datetime',
        'checktype' => 'integer',
        'is_pkl' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Checktype constants
    const TYPE_MASUK = 0;
    const TYPE_PULANG = 1;
    const TYPE_SAKIT = 2;
    const TYPE_IZIN = 3;
    const TYPE_ALPHA = 4;

    /**
     * Get the student by NIS.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'nis', 'nis');
    }

    /**
     * Get the dudi (for PKL attendance).
     */
    public function dudi(): BelongsTo
    {
        return $this->belongsTo(Dudi::class);
    }

    /**
     * Scope for today's attendance.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('checktime', today());
    }

    /**
     * Scope for check-in (checktype = 0).
     */
    public function scopeCheckIn($query)
    {
        return $query->where('checktype', self::TYPE_MASUK);
    }

    /**
     * Scope for check-out (checktype = 1).
     */
    public function scopeCheckOut($query)
    {
        return $query->where('checktype', self::TYPE_PULANG);
    }

    /**
     * Scope for PKL attendance.
     */
    public function scopePkl($query)
    {
        return $query->where('is_pkl', true);
    }

    /**
     * Scope for regular (non-PKL) attendance.
     */
    public function scopeRegular($query)
    {
        return $query->where('is_pkl', false);
    }

    /**
     * Check if this is a check-in.
     */
    public function isCheckIn(): bool
    {
        return $this->checktype === self::TYPE_MASUK;
    }

    /**
     * Check if this is a check-out.
     */
    public function isCheckOut(): bool
    {
        return $this->checktype === self::TYPE_PULANG;
    }

    /**
     * Get checktype label.
     */
    public function getChecktypeLabelAttribute(): string
    {
        return match ($this->checktype) {
            self::TYPE_MASUK => 'Masuk',
            self::TYPE_PULANG => 'Pulang',
            self::TYPE_SAKIT => 'Sakit',
            self::TYPE_IZIN => 'Izin',
            self::TYPE_ALPHA => 'Alpha',
            default => 'Unknown',
        };
    }
}
