<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone']; // Campos asignables en masa

    // Relación con equipos
    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}