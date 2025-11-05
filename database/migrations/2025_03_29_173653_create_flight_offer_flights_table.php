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
        Schema::create('flight_offer_flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('flight_offers')->onDelete('cascade');
            $table->string('flight_number');
            $table->dateTime('departure_date');
            $table->dateTime('return_date')->nullable();
            $table->string('departure_city');
            $table->string('arrival_city');
            $table->json('flight_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_offer_flights');
    }
};
