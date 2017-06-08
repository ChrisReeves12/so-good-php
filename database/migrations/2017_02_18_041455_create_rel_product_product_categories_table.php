<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelProductProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rel_product_product_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->index()->nullable(true);
            $table->integer('product_category_id')->index()->nullable(true);
            $table->integer('sort_order')->nullable(true);
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('product_category_id')->references('id')->on('product_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rel_product_product_categories');
    }
}
