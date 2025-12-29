<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $table = 'm_mapels';

    protected $fillable = [
        'nm_mapel',
        'alias',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active mapel.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
