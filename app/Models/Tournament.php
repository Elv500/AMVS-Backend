<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_date', 'end_date', 'allowed_hours_start', 'allowed_hours_end'];

    // Relación con partidos
    public function matches()
    {
        return $this->hasMany(Match::class);
    }
}