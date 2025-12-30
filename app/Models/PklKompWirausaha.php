<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklKompWirausaha extends Model
{
    protected $table = 'pkl_kompwirausaha';

    protected $fillable = [
        'm_jurusan_id',
        'nama',
    ];

    /**
     * Get the jurusan that owns this komponen wirausaha.
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'm_jurusan_id');
    }
}
