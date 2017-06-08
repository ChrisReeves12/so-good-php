<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable(true);
            $table->jsonb('specs')->nullable(true);
            $table->string('default_image')->nullable(true);
            $table->jsonb('images')->nullable(true);
            $table->string('model_number')->nullable(true);
            $table->boolean('is_inactive')->default(false);
            $table->boolean('affiliate_allowed')->default(false);
            $table->integer('vendor_id')->index()->nullable(true);
            $table->jsonb('cached_options')->nullable(true);
            $table->string('slug')->unique();
            $table->integer('default_item_id')->index()->nullable(true);
            $table->text('tags')->nullable(true);
            $table->timestamps();
            $table->foreign('default_item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
