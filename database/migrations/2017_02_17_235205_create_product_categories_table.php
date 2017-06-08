<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_inactive')->default(false);
            $table->integer('parent_category_id')->index()->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('slug')->unique();
            $table->text('tags')->nullable(true);
            $table->string('image')->nullable(true);
            $table->string('banner')->nullable(true);
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
        Schema::dropIfExists('product_categories');
    }
}
