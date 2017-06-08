<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('summary')->nullable(true);
            $table->longText('body')->default('');
            $table->boolean('is_published')->default(false);
            $table->timestamp('date_published')->nullable(true);
            $table->integer('article_category_id')->nullable(true);
            $table->string('slug')->unique();
            $table->timestamps();

            $table->foreign('article_category_id')->references('id')->on('article_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
