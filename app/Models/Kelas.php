<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nm_kls',
        'alias',
    ];

    /**
     * Get students in this class.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'kelas_id');
    }
}
