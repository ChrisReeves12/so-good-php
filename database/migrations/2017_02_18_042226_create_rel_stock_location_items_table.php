<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelStockLocationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rel_stock_location_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->index()->nullable(true);
            $table->integer('stock_location_id')->index()->nullable(true);
            $table->integer('quantity_available')->default(0);
            $table->boolean('can_preorder')->default(false);
            $table->boolean('is_inactive')->default(false);
            $table->integer('quantity_reserved')->default(0);
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('stock_location_id')->references('id')->on('stock_locations');
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
        Schema::dropIfExists('rel_stock_location_items');
    }
}
