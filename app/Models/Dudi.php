<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dudi extends Model
{
    use HasFactory;

    protected $table = 'dudis';

    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'bidang_usaha',
    ];

    /**
     * Get PKL records for this Dudi
     */
    public function pkls(): HasMany
    {
        return $this->hasMany(Pkl::class, 'dudi_id');
    }
}
