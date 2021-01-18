<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string("transfer_booking_id")->nullable();
            $table->string("transfer_courier_id")->nullable();
            $table->string("transfer_status")->nullable();
            $table->string("transfer_tracking_id")->nullable();
            $table->string("action_status")->nullable();
            $table->string("curier_status")->nullable();
            $table->string("cod_amount")->nullable();
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
        Schema::dropIfExists('transfers');
    }
}
