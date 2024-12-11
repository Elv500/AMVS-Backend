<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    // Campos asignables en masa
    protected $fillable = ['name', 'coach_id'];

    // Relación con el modelo Coach
    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }
}