<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_express');
            $table->string('carrier_name')->default('N/A');
            $table->string('api_identifier')->default('N/A')->index();
            $table->boolean('is_inactive')->default(false);
            $table->integer('transit_time')->default(0);
            $table->string('calculation_method')->default('flat_rate');
            $table->float('flat_rate')->default(0);
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
        Schema::dropIfExists('shipping_methods');
    }
}
