<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  
    public function up()
    {
        Schema::create('flight_search_results', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // direct_single, direct_multiple, etc.
            $table->json('search_input')->nullable();    // tout le POST du formulaire
            $table->json('results')->nullable();         // résultat Amadeus
            $table->integer('seats')->nullable();
            $table->decimal('price_adult', 12, 2)->nullable();
            $table->decimal('price_child', 12, 2)->nullable();
            $table->decimal('price_baby', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('flight_search_results');
    }
};
