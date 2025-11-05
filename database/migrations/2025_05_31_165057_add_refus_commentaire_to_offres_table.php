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
        Schema::table('offres', function ($table) {
            $table->text('refus_commentaire')->nullable()->after('statut');
        });
    }
    
    public function down()
    {
        Schema::table('offres', function ($table) {
            $table->dropColumn('refus_commentaire');
        });
    }
};
