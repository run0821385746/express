<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersDropcentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_drop_centers', function (Blueprint $table) {
            $table->id();
            $table->string("transfer_dropcenter_booking_id")->nullable();
            $table->string("transfer_dropcenter_id")->nullable();
            $table->string("transfer_dropcenter_status")->nullable();
            $table->string("transfer_dropcenter_tracking_id")->nullable();
            $table->string("transfer_dropcenter_sender_id")->nullable();
            $table->string("transfer_bill_no_ref")->nullable();
            $table->string("transfer_bill_id_ref")->nullable();
            $table->string("transfer_dropcenter_tracking_no")->nullable();
            $table->string("action_status")->nullable();
            $table->string("trackDC_status")->nullable();
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
        Schema::dropIfExists('transfer_drop_centers');
    }
}
