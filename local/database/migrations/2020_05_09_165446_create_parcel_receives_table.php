<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelReceivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_receives', function (Blueprint $table) {
            $table->id();
            $table->string("receiver_tracking_id")->nullable();
            $table->string("receiver_image")->nullable();
            $table->string("receiver_signatur")->nullable();
            $table->string("receiver_name")->nullable();
            $table->string("receiver_type_id")->nullable();
            $table->string("receiver_other_type_name")->nullable();

            $table->string("receiver_branch_id")->nullable();
            $table->string("receiver_currier_id")->nullable();
            $table->string("receiver_status")->nullable();
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
        Schema::dropIfExists('parcel_receives');
    }
}
