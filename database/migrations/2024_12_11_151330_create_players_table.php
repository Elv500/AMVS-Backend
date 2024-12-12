<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('team_id')->nullable(); // Relación con equipos
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
            $table->string('position')->nullable();
            $table->integer('age')->nullable();
            $table->string('ci_number')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('players');
    }
}