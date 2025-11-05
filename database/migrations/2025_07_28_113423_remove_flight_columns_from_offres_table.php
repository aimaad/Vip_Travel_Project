<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFlightColumnsFromOffresTable extends Migration
{
    public function up()
    {
        Schema::table('offres', function (Blueprint $table) {
            $table->dropColumn([
                'places',
                'price_adult',
                'price_child',
                'price_baby',
                'flight_type',
                'airline_logo',
                'flight_number_aller',
                'departure_city_aller',
                'arrival_city_aller',
                'departure_date_aller',
                'departure_time_aller',
                'arrival_date_aller',
                'arrival_time_aller',
                'flight_number_retour',
                'departure_city_retour',
                'arrival_city_retour',
                'departure_date_retour',
                'departure_time_retour',
                'arrival_date_retour',
                'arrival_time_retour',
                'airline_logo_retour'
            ]);
        });
    }

    public function down()
    {
        Schema::table('offres', function (Blueprint $table) {
            // Ici tu peux redéfinir les colonnes si besoin de rollback
            $table->integer('places')->nullable();
            $table->decimal('price_adult', 10, 2)->nullable();
            $table->decimal('price_child', 10, 2)->nullable();
            $table->decimal('price_baby', 10, 2)->nullable();
            $table->string('flight_type', 40)->nullable();
            $table->string('airline_logo')->nullable();
            $table->string('flight_number_aller', 20)->nullable();
            $table->string('departure_city_aller', 20)->nullable();
            $table->string('arrival_city_aller', 20)->nullable();
            $table->date('departure_date_aller')->nullable();
            $table->time('departure_time_aller')->nullable();
            $table->date('arrival_date_aller')->nullable();
            $table->time('arrival_time_aller')->nullable();
            $table->string('flight_number_retour', 20)->nullable();
            $table->string('departure_city_retour', 20)->nullable();
            $table->string('arrival_city_retour', 20)->nullable();
            $table->date('departure_date_retour')->nullable();
            $table->time('departure_time_retour')->nullable();
            $table->date('arrival_date_retour')->nullable();
            $table->time('arrival_time_retour')->nullable();
            $table->string('airline_logo_retour')->nullable();
        });
    }
}
