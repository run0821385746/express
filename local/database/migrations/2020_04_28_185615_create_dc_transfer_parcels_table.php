<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcTransferParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_transfer_parcels', function (Blueprint $table) {
            $table->id();
            $table->string('dc_transfer_no');
            $table->string('dc_transfer_sender');
            $table->string('dc_transfer_date');
            $table->string('dc_transfer_qty');
            $table->string('dc_transfer_status');
            $table->string('dc_transfer_isDone');
            $table->string('dc_transfer_receiver');
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
        Schema::dropIfExists('dc_transfer_parcels');
    }
}
