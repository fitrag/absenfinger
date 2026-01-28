<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruKesejahteraan extends Model
{
    use HasFactory;

    protected $fillable = ['m_guru_id', 'jenis', 'nama', 'penyelenggara', 'dari_th', 'sampai_th', 'status'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
