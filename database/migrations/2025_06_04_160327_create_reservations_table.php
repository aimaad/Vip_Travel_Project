<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offre_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nom_client');
            $table->string('email');
            $table->string('telephone')->nullable();
            $table->integer('nombre_personnes');
            $table->date('date_arrivee');
            $table->date('date_depart');
            $table->text('commentaire')->nullable();
            $table->string('statut')->default('en_attente'); // en_attente, confirmée, annulée
            $table->timestamps();

            $table->foreign('offre_id')->references('id')->on('offres')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}