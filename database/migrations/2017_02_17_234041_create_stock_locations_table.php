<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone_number')->nullable(true);
            $table->string('address_id')->nullable(true);
            $table->boolean('is_dropship')->nullable(true)->default(false);
            $table->boolean('is_main_location')->default(false);
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
        Schema::dropIfExists('stock_locations');
    }
}
