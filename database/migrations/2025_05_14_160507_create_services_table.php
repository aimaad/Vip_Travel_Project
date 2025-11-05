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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('type_service'); // Exemple : transfert, excursion...
            $table->date('date_service');
            $table->text('description');
            $table->decimal('prix', 10, 2);
            $table->integer('capacite');
            $table->enum('type', ['inclus', 'exclus']); // Type d'inclusion
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
