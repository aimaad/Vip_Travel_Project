<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffresTable extends Migration
{
    public function up()
    {
        Schema::create('offres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_scraping_id')->constrained('hotel_scrapings'); // Relier à la table hotel_scrapings
            $table->integer('total_rooms');
            $table->json('room_types'); // Colonne pour stocker les types de chambres en format JSON
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offres');
    }
};