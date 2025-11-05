<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedColumnsFromUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop columns that are not used in the Blade template
            $table->dropColumn([
                
                // Add any other columns that are not used in your Blade template
            ]);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Optionally, you can re-add the columns if you want to roll back the migration
            $table->string('display_name')->nullable();
            $table->string('user_name')->nullable();
            // Add back any other columns you dropped
        });
    }
}
