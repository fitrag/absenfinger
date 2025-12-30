<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklKompSoft extends Model
{
    protected $table = 'pkl_kompsofts';

    protected $fillable = [
        'm_jurusan_id',
        'nama',
    ];

    /**
     * Get the jurusan that owns this komponen soft skill.
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'm_jurusan_id');
    }
}
