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
            if (!Schema::hasColumn('flight_searches', 'type')) {
                $table->string('type')->nullable()->default(null);
            }
            if (!Schema::hasColumn('flight_searches', 'results')) {
                $table->json('results')->nullable();
            }
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
