<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_id')->index()->nullable(true);
            $table->float('discount_amount')->default(0);
            $table->integer('discount_id')->index()->nullable(true)->nullable(true);
            $table->string('ip_address')->nullable(true);
            $table->integer('billing_address_id')->index()->nullable(true);
            $table->integer('shipping_address_id')->index()->nullable(true);
            $table->float('tax')->default(0);
            $table->float('total')->default(0);
            $table->float('sub_total')->default(0);
            $table->float('shipping_total')->default(0);
            $table->float('tax_rate')->nullable(true);
            $table->string('first_name')->nullable(true);
            $table->string('last_name')->nullable(true);
            $table->string('email')->nullable(true);
            $table->string('phone_number')->nullable(true);
            $table->string('transactionable_type');
            $table->integer('transactionable_id')->index()->nullable(true);
            $table->foreign('entity_id')->references('id')->on('entities');
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
        Schema::dropIfExists('transactions');
    }
}