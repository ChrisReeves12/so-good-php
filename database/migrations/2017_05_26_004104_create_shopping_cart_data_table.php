<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShoppingCartDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_cart_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shopping_cart_id')->nullable(true);
            $table->string('email')->nullable(true);
            $table->boolean('parsed')->nullable(true)->default(false);
            $table->jsonb('line_items');
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
        Schema::dropIfExists('shopping_cart_data');
    }
}
