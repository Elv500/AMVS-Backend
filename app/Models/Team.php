<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    // Campos asignables en masa
    protected $fillable = ['name', 'coach_id', 'matches_played', 'matches_won', 'matches_lost', 'points', 'logo'];

    // Relación con el modelo Coach
    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }
    
    //Relacion con el modelo Player
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function matches()
    {
        return $this->hasMany(Match::class, 'local_team_id')
                    ->orWhere('visitor_team_id', $this->id);
    }
}