<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    use HasFactory;

    protected $table = 'sertifikats';

    protected $fillable = [
        'bgFront',
        'bgBack',
        'tgl_mulai',
        'tgl_selesai',
        'tgl_cetak',
        'm_tp_id',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'tgl_cetak' => 'date',
    ];

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'm_tp_id');
    }
}
