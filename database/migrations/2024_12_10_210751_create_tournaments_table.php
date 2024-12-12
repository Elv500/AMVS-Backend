<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentsTable extends Migration
{
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del torneo
            $table->date('start_date'); // Fecha de inicio del torneo
            $table->date('end_date'); // Fecha de fin del torneo
            $table->time('allowed_hours_start')->default('08:00:00'); // Hora de inicio permitida
            $table->time('allowed_hours_end')->default('20:00:00'); // Hora de fin permitida
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tournaments');
    }
}