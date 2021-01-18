<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_contacts', function (Blueprint $table) {
            $table->id();
            $table->string("history_tracking_id")->nullable();
            $table->string("history_currier_id")->nullable();
            $table->string("history_reason_id")->nullable();
            $table->string("history_timing_call")->nullable();
            $table->string("history_status")->nullable();
            $table->string("history_branch_id")->nullable();
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
        Schema::dropIfExists('history_contacts');
    }
}
