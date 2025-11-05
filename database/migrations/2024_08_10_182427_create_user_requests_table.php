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
        Schema::create('role_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'agent' ou 'distributor'
            $table->string('agency_name')->nullable();
            $table->string('other_agency_name')->nullable();
            $table->string('iata_office_id')->nullable();
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_requests');
    }
};
