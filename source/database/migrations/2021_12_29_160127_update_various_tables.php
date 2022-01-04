<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVariousTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aboutuspage', function (Blueprint $table) {
            $table->string('phone',30)->nullable()->after('description'); 
        });
        Schema::table('coupon', function (Blueprint $table) {
            $table->double('max_discount')->nullable()->after('amount'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
