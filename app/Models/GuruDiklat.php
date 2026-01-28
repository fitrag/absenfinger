<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruDiklat extends Model
{
    use HasFactory;

    protected $fillable = [
        'm_guru_id',
        'jns_diklat',
        'nama',
        'penyelenggara',
        'thn',
        'peran',
        'tingkat',
        'brp_jam',
        'sertifikat_diklat'
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
