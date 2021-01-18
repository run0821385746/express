<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubtrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('subtracking_no');
            $table->string('subtracking_booking_id');
            $table->string('subtracking_tracking_id')->nullable();
            $table->string('subtracking_dimension_type');
            $table->string('subtracking_cod');
            $table->string('subtracking_price');
            $table->string('subtracking_status');
            $table->string('subtracking_parcel_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_trackings');
    }
}
