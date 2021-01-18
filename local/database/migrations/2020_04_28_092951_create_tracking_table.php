<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trackings', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_no');
            $table->string('tracking_booking_id');
            $table->string('tracking_receiver_id')->nullable();
            $table->string('tracking_parcel_type')->nullable();
            $table->string('tracking_status');
            $table->string('tracking_amount')->nullable();
            $table->string('tracking_send_status')->nullable();
            // $table->string('tracking_cod_amount')->nullable();
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
        Schema::dropIfExists('trackings');
    }
}
