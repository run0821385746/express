<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string("emp_id");
            $table->string("sum_report_menu");
            $table->string("receive_menu");
            $table->string("parcel_care_menu");
            $table->string("transfer_parcel_menu");
            $table->string("request_service_menu");
            $table->string("receive_parcel_from_dc_menu");
            $table->string("parcel_status_wrong_menu");
            $table->string("basic_information_menu");
            $table->string("permission_status");
            $table->string("branch_id");
            $table->string("update_by");
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
        Schema::dropIfExists('permissions');
    }
}
