<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruAnak extends Model
{
    use HasFactory;

    protected $fillable = [
        'm_guru_id',
        'nama',
        'status',
        'jenjang',
        'nisn',
        'jk',
        'tmpt_lhr',
        'tgl_lhr',
        'thn_masuk'
    ];

    protected $casts = ['tgl_lhr' => 'date'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
