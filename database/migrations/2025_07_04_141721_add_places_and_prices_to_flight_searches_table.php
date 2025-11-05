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
        Schema::table('flight_searches', function (Blueprint $table) {
            $table->integer('places')->nullable();
            $table->decimal('price_adult', 10, 2)->nullable();
            $table->decimal('price_child', 10, 2)->nullable();
            $table->decimal('price_baby', 10, 2)->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('flight_searches', function (Blueprint $table) {
            $table->dropColumn(['places', 'price_adult', 'price_child', 'price_baby']);
        });
    }
};
