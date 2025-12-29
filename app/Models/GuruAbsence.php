<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruAbsence extends Model
{
    use HasFactory;

    protected $table = 'guru_absences';

    protected $fillable = [
        'guru_id',
        'date',
        'status', // sakit, izin, alpha
        'kelas_ids',
        'jam_ke',
        'ket',
    ];

    protected $casts = [
        'kelas_ids' => 'array',
    ];

    /**
     * Get the guru associated with the absence.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Get the kelas names for display.
     */
    public function getKelasNamesAttribute()
    {
        if (empty($this->kelas_ids)) {
            return '-';
        }
        return \App\Models\Kelas::whereIn('id', $this->kelas_ids)->pluck('nm_kls')->implode(', ');
    }
}
