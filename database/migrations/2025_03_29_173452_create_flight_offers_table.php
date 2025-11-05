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
        Schema::create('flight_offers', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'direct_single', 'direct_multiple', 'multi_flight_single', 'multi_flight_multiple'
            $table->integer('seats_available');
            $table->string('status')->default('draft');
            $table->integer('author_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_offers');
    }
};
