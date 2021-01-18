<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDimensionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dimension_histories', function (Blueprint $table) {
            $table->id();
            
            $table->string("dimension_history_tracking_id")->nullable();
            $table->string("dimension_history_subtracking_id")->nullable();
            $table->string("dimension_history_width")->nullable();
            $table->string("dimension_history_hight")->nullable();
            $table->string("dimension_history_length")->nullable();
            $table->string("dimension_history_total_dimension")->nullable();
            $table->string("dimension_history_weigth")->nullable();
            $table->string("dimension_history_status")->nullable();

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
        Schema::dropIfExists('dimension_histories');
    }
}
