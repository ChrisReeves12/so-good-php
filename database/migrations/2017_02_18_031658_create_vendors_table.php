<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_inactive')->default(false);
            $table->text('description')->nullable(true);
            $table->string('email')->nullable(true);
            $table->string('website')->nullable(true);
            $table->integer('address_id')->index()->nullable(true);
            $table->boolean('is_dropshipper')->default(false);
            $table->string('image')->nullable(true);
            $table->string('phone_number')->nullable(true);
            $table->timestamps();
            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}
