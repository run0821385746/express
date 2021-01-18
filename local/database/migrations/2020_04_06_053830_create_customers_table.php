<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string("cust_name");
            $table->text("cust_address");
            $table->string("cust_sub_district");
            $table->string("cust_district");
            $table->string("cust_province");
            $table->string("cust_postcode");
            $table->string("cust_phone");
            $table->string("cust_status");
            $table->string("cust_cod_register_status")->nullable();
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
        Schema::dropIfExists('customers');
    }
}
