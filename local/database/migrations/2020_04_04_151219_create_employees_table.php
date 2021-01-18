<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('emp_firstname');
            $table->string('emp_lastname');
            $table->string('emp_address');
            $table->string('emp_sub_district');
            $table->string('emp_district');
            $table->string('emp_province');
            $table->string('emp_postcode');
            $table->string('emp_phone');
            $table->string('emp_position');
            $table->string('emp_status');
            $table->string('emp_branch_id');
            $table->string('emp_image')->nullable();
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
        Schema::dropIfExists('employees');
    }
}
