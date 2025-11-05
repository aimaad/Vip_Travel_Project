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
        Schema::create('flight_selections', function (Blueprint $table) {
            $table->id();
            $table->string('outbound_flight_number');
            $table->string('return_flight_number')->nullable();
            $table->string('departure_city');
            $table->string('arrival_city');
            $table->dateTime('outbound_departure_time');
            $table->dateTime('outbound_arrival_time');
            $table->dateTime('return_departure_time')->nullable();
            $table->dateTime('return_arrival_time')->nullable();
            $table->decimal('price_total', 10, 2);
            $table->string('currency');
            $table->decimal('adult_price', 10, 2);
            $table->decimal('child_price', 10, 2);
            $table->decimal('infant_price', 10, 2);
            $table->integer('seats');
            $table->string('travel_class');
            $table->json('flight_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer');
    }
};
