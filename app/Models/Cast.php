<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    use HasFactory;

    protected $table = 'casts';
    protected $fillable = [
        'name',
        'age',
        'bio',
        'foto',
    ];

    /**
     * Relasi ke model Film melalui perans (many-to-many)
     */
    public function films()
    {
        return $this->belongsToMany(Film::class, 'perans', 'cast_id', 'film_id')->withPivot('actor')->withTimestamps();
    }

    /**
     * Relasi ke model Peran
     */
    public function perans()
    {
        return $this->hasMany(Peran::class, 'cast_id');
    }
}
