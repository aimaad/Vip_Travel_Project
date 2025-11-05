<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['single', 'double', 'triple']);
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->integer('kids')->default(0);
            $table->integer('babies')->default(0);
            $table->decimal('price', 10, 2);
            $table->integer('available_rooms');
            $table->enum('pension', ['RO', 'PDJ', 'DP', 'PC']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
