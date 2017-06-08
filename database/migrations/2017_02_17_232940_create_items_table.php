<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku')->unique(true);
            $table->float('list_price')->nullable(true)->default(0);
            $table->float('store_price')->default(0);
            $table->boolean('is_inactive')->default(false);
            $table->string('calculated_stock_status')->nullable(true);
            $table->string('stock_status_override')->nullable(true)->default('none');
            $table->string('image')->nullable(true);
            $table->integer('product_id')->index()->nullable(true);
            $table->jsonb('details')->nullable(true);
            $table->float('weight')->default(0);
            $table->boolean('ships_alone')->default(false);
            $table->boolean('is_default')->default(false);
            $table->integer('main_stock_location_id')->index()->nullable(true);
            $table->string('upc')->nullable(true);
            $table->string('isbn')->nullable(true);
            $table->string('ean')->nullable(true);
            $table->float('cost')->nullable(true);
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
        Schema::dropIfExists('items');
    }
}
