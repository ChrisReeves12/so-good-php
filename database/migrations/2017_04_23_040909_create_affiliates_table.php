<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('main_image')->nullable();
            $table->string('list_page_image')->nullable();
            $table->string('website')->nullable();
            $table->string('type');
            $table->string('slug')->unique();
            $table->string('affiliate_tag')->unique();
            $table->jsonb('social_media_links')->nullable();
            $table->jsonb('images')->nullable();
            $table->jsonb('videos')->nullable();
            $table->text('tags')->nullable();
            $table->text('short_bio')->nullable();
            $table->boolean('is_inactive')->default(false);
            $table->longText('long_bio')->nullable();
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
        Schema::dropIfExists('affiliates');
    }
}
