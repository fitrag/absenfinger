<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruPangkatGol extends Model
{
    use HasFactory;

    protected $fillable = [
        'm_guru_id',
        'pangkat_gol',
        'no_sk',
        'tgl_pangkat',
        'tmt_pangkat',
        'masa_kerja_tahun',
        'masa_kerja_bln'
    ];

    protected $casts = ['tgl_pangkat' => 'date', 'tmt_pangkat' => 'date'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
