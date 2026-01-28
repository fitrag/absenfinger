<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruInpasing extends Model
{
    use HasFactory;

    protected $fillable = [
        'm_guru_id',
        'pangkat_gol',
        'no_sk_inpasing',
        'tgl_sk',
        'tmt_sk',
        'angka_kredit',
        'masa_kerja_thn',
        'masa_kerja_bln'
    ];

    protected $casts = ['tgl_sk' => 'date', 'tmt_sk' => 'date', 'angka_kredit' => 'decimal:2'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
