<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruPendidikan extends Model
{
    use HasFactory;

    protected $fillable = [
        'm_guru_id',
        'bidang_studi',
        'jenjang_pendidikan',
        'gelar',
        'satuan_pendidikan',
        'thn_masuk',
        'nim',
        'mata_kuliah',
        'semester',
        'ipk'
    ];

    protected $casts = ['ipk' => 'decimal:2'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
