<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'team_id', 'position', 'age', 'ci_number'];

    // Relación con el equipo
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}