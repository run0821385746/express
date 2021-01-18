<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDropCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drop_centers', function (Blueprint $table) {
            $table->id();
            $table->string("drop_center_name");
            $table->string("drop_center_address");
            $table->string("drop_center_sub_district");
            $table->string("drop_center_district");
            $table->string("drop_center_province");
            $table->string("drop_center_postcode");
            $table->string("drop_center_phone");
            $table->string("drop_center_status");
            $table->string("drop_center_name_initial");
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
        Schema::dropIfExists('drop_centers');
    }
}
