<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruAttendance extends Model
{
    use HasFactory;

    protected $table = 'guru_attendances';

    protected $fillable = [
        'guru_id',
        'date',
        'status', // hadir, sakit, izin, alpha
        'check_in',
        'check_out',
        'notes',
    ];

    /**
     * Get the guru associated with the attendance.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}
