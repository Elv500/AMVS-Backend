<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;

    protected $fillable = ['local_team_id', 'visitor_team_id', 'field_id', 'date', 'time', 'state', 'sets', 'tournament_id'];

    protected $casts = [
        'sets' => 'array', // Cast para que 'sets' siempre sea un arreglo
    ];
    
    public function localTeam()
    {
        return $this->belongsTo(Team::class, 'local_team_id');
    }

    public function visitorTeam()
    {
        return $this->belongsTo(Team::class, 'visitor_team_id');
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}