<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Booking\Models\Service;
use Modules\Booking\Models\ServiceTranslation;
use Modules\Location\Models\LocationCategory;
use Modules\Location\Models\LocationCategoryTranslation;

class UpdateCore190 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_wishlist');
        Schema::dropIfExists('bravo_booking_payments');
    }
}
