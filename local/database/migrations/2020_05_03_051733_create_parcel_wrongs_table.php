<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelWrongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_wrongs', function (Blueprint $table) {
            $table->id();
            $table->string("wrong_booking_id")->nullable();
            $table->string("wrong_tracking_id")->nullable();
            $table->string("wrong_subtracking_id")->nullable();
            $table->string("wrong_problem_detail")->nullable();
            $table->string("wrong_description_solve")->nullable();
            $table->string("wrong_status")->nullable();
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
        Schema::dropIfExists('parcel_wrongs');
    }
}
