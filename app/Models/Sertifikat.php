<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    use HasFactory;

    protected $table = 'sertifikats';

    protected $fillable = [
        'pkl_id',
        'nomor_sertifikat',
        'tanggal_terbit',
        'nilai',
        'predikat',
        'file_sertifikat',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    /**
     * Get the PKL record
     */
    public function pkl(): BelongsTo
    {
        return $this->belongsTo(Pkl::class);
    }

    /**
     * Get the creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get predikat badge
     */
    public function getPredikatBadgeAttribute(): string
    {
        return match ($this->predikat) {
            'Sangat Baik' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
            'Baik' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
            'Cukup' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
            'Kurang' => 'bg-red-500/20 text-red-400 border-red-500/30',
            default => 'bg-slate-500/20 text-slate-400 border-slate-500/30',
        };
    }
}
