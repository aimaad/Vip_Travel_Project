<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferFlightLegsTable extends Migration
{
    public function up()
    {
        Schema::create('offer_flight_legs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_flight_id')->constrained('offer_flights')->onDelete('cascade');
            $table->enum('direction', ['outbound', 'return']);
            $table->string('flight_number', 20);
            $table->string('departure_city', 20);
            $table->string('arrival_city', 20);
            $table->date('departure_date')->nullable();
            $table->date('arrival_date')->nullable();
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->string('carrier_code', 10)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offer_flight_legs');
    }
}