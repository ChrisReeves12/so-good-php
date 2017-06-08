<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name')->nullable(true);
            $table->string('last_name')->nullable(true);
            $table->string('line_1');
            $table->string('line_2')->nullable(true);
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->string('country')->default('US');
            $table->string('phone_number')->nullable(true);
            $table->string('company')->nullable();
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
        Schema::dropIfExists('addresses');
    }
}
