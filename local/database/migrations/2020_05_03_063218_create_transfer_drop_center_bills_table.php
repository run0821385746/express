<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferDropCenterBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_drop_center_bills', function (Blueprint $table) {
            $table->id();
            $table->string("transfer_bill_no")->nullable();
            $table->string("transfer_sender_id")->nullable();
            $table->string("transfer_recriver_id")->nullable();
            $table->string("transfer_bill_status")->nullable();
            
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
        Schema::dropIfExists('transfer_drop_center_bills');
    }
}
