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
        Schema::table('flight_search_result_amadeuses', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('search_input');
        });
    }
    
    public function down()
    {
        Schema::table('flight_search_result_amadeuses', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
