<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruAjar extends Model
{
    use HasFactory;

    protected $table = 'm_guruajars';

    protected $fillable = [
        'guru_id',
        'mapel_id',
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
     * Get the mapel.
     */
    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }

    /**
     * Scope for active.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
