<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Walas extends Model
{
    use HasFactory;

    protected $table = 'm_walas';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'tp_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the guru.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    /**
     * Get the kelas.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Get the tahun pelajaran.
     */
    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'tp_id');
    }

    /**
     * Scope for active.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
