<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruPiket extends Model
{
    use HasFactory;

    protected $table = 'm_guru_piket';

    protected $fillable = [
        'guru_id',
        'hari',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the guru that owns the piket assignment.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Scope for active piket assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Days of the week available for piket.
     */
    public static function getDays(): array
    {
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    }
}
