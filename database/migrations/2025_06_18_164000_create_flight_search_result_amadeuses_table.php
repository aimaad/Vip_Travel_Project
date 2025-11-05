<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flight_search_result_amadeuses', function (Blueprint $table) {
            $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('type')->nullable();
        $table->json('search_input')->nullable();
        $table->integer('places')->nullable();
        $table->decimal('price_adult', 10, 2)->nullable();
        $table->decimal('price_child', 10, 2)->nullable();
        $table->decimal('price_baby', 10, 2)->nullable();
        $table->longText('results_html')->nullable(); // le HTML de résultat
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_search_result_amadeuses');
    }
};
