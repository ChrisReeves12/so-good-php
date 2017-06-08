<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payment_method')->nullable(true);
            $table->string('auth_code')->nullable(true);
            $table->float('payment_fees')->default(0);
            $table->string('order_source');
            $table->text('memo')->nullable(true);
            $table->string('payment_info')->nullable(true);
            $table->jsonb('discount_codes')->nullable(true);
            $table->boolean('is_fraud_detected')->default(false);
            $table->integer('transaction_id')->index()->nullable(true);
            $table->string('status')->default('pending');
            $table->jsonb('reserved_inventory')->nullable(true);
            $table->boolean('shipping_calc_needed')->default(false);
            $table->jsonb('tracking_numbers')->nullable(true);
            $table->string('marketing_channel')->default('N/A');
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
        Schema::dropIfExists('sales_orders');
    }
}
