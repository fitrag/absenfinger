<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruJabatanFungsional extends Model
{
    use HasFactory;

    protected $fillable = ['m_guru_id', 'jabatan_fungsional', 'sk_jabatan', 'tmt_sk'];

    protected $casts = ['tmt_sk' => 'date'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
