<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_prices', function (Blueprint $table) {
            $table->id();
            $table->string("parcel_total_dimension");
            $table->string("parcel_total_weight");
            $table->string("parcel_price");
            $table->string("parcel_price_status");
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
        Schema::dropIfExists('parcel_prices');
    }
}
