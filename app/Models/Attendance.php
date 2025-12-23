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
    ];

    protected $casts = [
        'checktime' => 'datetime',
        'checktype' => 'integer',
    ];

    /**
     * Get the student by NIS.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'nis', 'nis');
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
        return $query->where('checktype', 0);
    }

    /**
     * Scope for check-out (checktype = 1).
     */
    public function scopeCheckOut($query)
    {
        return $query->where('checktype', 1);
    }

    /**
     * Check if this is a check-in.
     */
    public function isCheckIn(): bool
    {
        return $this->checktype === 0;
    }

    /**
     * Check if this is a check-out.
     */
    public function isCheckOut(): bool
    {
        return $this->checktype === 1;
    }

    /**
     * Get checktype label.
     */
    public function getChecktypeLabelAttribute(): string
    {
        return $this->checktype === 0 ? 'Masuk' : 'Pulang';
    }
}
