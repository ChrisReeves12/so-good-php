<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressForeignKeyToEntities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entities', function(Blueprint $table) {
            $table->foreign('billing_address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->foreign('shipping_address_id')->references('id')->on('addresses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entities', function(Blueprint $table) {
            $table->dropForeign(['billing_address_id']);
            $table->dropForeign(['shipping_address_id']);
        });
    }
}
