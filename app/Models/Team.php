<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    //Permite asignación masiva en los campos 'name' y 'coach'
    protected $fillable = ['name', 'coach'];
}
