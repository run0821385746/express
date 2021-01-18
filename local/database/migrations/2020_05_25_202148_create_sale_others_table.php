<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOthersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_others', function (Blueprint $table) {
            $table->id();
            $table->string("sale_other_product_id")->nullable();
            $table->string("sale_other_price")->nullable();
            $table->string("sale_other_branch_id")->nullable();
            $table->string("sale_other_booking_id")->nullable();
            $table->string("sale_other_tr_id")->nullable();
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
        Schema::dropIfExists('sale_others');
    }
}
