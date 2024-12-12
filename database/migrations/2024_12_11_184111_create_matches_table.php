<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('local_team_id'); // Equipo local
            $table->unsignedBigInteger('visitor_team_id'); // Equipo visitante
            $table->unsignedBigInteger('field_id'); // Relación con cancha
            $table->unsignedBigInteger('tournament_id')->nullable(); // Relación con torneo
            $table->date('date'); // Fecha del partido
            $table->time('time'); // Hora del partido
            $table->string('state')->default('Pendiente'); // Estado del partido
            $table->json('sets')->nullable();
            $table->timestamps();

            // Relaciones
            $table->foreign('local_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('visitor_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('field_id')->references('id')->on('fields')->onDelete('cascade');
            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('matches');
    }
}