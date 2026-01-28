<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruSertifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'm_guru_id',
        'jenis_serti',
        'no',
        'thn',
        'bidang_studi',
        'nrg',
        'no_pes'
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
