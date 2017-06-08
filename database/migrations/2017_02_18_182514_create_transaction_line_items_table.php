<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_line_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->index()->nullable(true);
            $table->integer('transaction_line_itemable_id')->index()->nullable(true);
            $table->string('transaction_line_itemable_type');
            $table->float('unit_price')->default(0);
            $table->float('total_price')->default(0);
            $table->float('discount_amount')->default(0);
            $table->float('tax_rate')->default(0)->nullable(true);
            $table->float('tax')->default(0);
            $table->float('shipping_charge')->default(0);
            $table->float('sub_total')->default(0);
            $table->string('name');
            $table->integer('quantity')->default(0);
            $table->string('status');
            $table->integer('transaction_id')->index()->nullable(true);
            $table->integer('ship_from_location_id')->index()->nullable(true);
            $table->integer('shipping_method_id')->nullable(true)->index();
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods');
            $table->foreign('transaction_id')->references('id')->on('transactions');
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
        Schema::dropIfExists('transaction_line_items');
    }
}
