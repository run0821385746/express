<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_services', function (Blueprint $table) {
            $table->id();
            $table->string("request_booking_id")->nullable();
            $table->string("request_sender_id")->nullable();
            $table->string("request_currier_id")->nullable();
            $table->string("request_status")->nullable();
            $table->string("branch_id")->nullable();
            $table->string("request_parcel_qty")->nullable();
            $table->string("action_status")->nullable();
            $table->string("request_booking_no")->nullable();
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
        Schema::dropIfExists('request_services');
    }
}
