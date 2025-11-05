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
        Schema::table('offres', function (Blueprint $table) {
            $table->json('service_ids')->nullable()->after('id'); // ou after('nom_colonne') selon ton besoin
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('offres', function (Blueprint $table) {
            $table->dropColumn('service_ids');
        });
    }
    
};
