<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_create_hotel_scrapings_table.php
public function up()
{
    Schema::create('hotel_scrapings', function (Blueprint $table) {
        $table->id();
        $table->string('hotel_name')->index();
        $table->string('url');
        $table->json('images');
        $table->string('address')->nullable();
        $table->string('rating')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_scrapings');
    }
};
