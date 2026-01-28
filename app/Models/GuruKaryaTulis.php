<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruKaryaTulis extends Model
{
    use HasFactory;

    protected $table = 'guru_karya_tuliss';

    protected $fillable = ['m_guru_id', 'judul', 'thn_pembuatan', 'publikasi', 'ket', 'url_publikasi'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}

