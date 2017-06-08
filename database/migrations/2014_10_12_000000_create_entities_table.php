<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password_digest')->nullable(true);
            $table->string('role')->default('customer');
            $table->string('ip_address')->nullable(true);
            $table->boolean('is_fraudulent')->default(false);
            $table->integer('shipping_address_id')->nullable(true);
            $table->integer('billing_address_id')->nullable(true);
            $table->string('token')->nullable(true);
            $table->string('phone_number')->nullable(true);
            $table->string('status')->default('unverified');
            $table->boolean('is_inactive')->default(false);
            $table->timestamps();

            $table->index(['email', 'password_digest']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities');
    }
}
