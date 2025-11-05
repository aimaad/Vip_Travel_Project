<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOfferFlightLegsTable extends Migration
{
    public function up()
    {
        Schema::table('offer_flight_legs', function (Blueprint $table) {
            // Ajoute la colonne airline_logo
            $table->string('airline_logo')->nullable()->after('carrier_code');
            // Enlève la colonne price
            $table->dropColumn('price');
        });
    }

    public function down()
    {
        Schema::table('offer_flight_legs', function (Blueprint $table) {
            // Enlève la colonne airline_logo
            $table->dropColumn('airline_logo');
            // Remet la colonne price
            $table->string('price')->nullable();
        });
    }
}