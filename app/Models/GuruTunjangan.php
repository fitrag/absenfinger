<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruTunjangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'm_guru_id',
        'jenis',
        'nama',
        'instansi',
        'sk_tunjangan',
        'tgl_sk',
        'semester',
        'sumber_dana',
        'dari_th',
        'sampai_th',
        'nominal',
        'status'
    ];

    protected $casts = ['tgl_sk' => 'date', 'nominal' => 'decimal:2'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
