<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('popups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false);
            $table->string('internal_name')->unique()->nullable(false);
            $table->string('cookie_name')->unique()->nullable(false);
            $table->longText('body')->nullable(true);
            $table->integer('width')->nullable(true);
            $table->integer('height')->nullable(true);
            $table->jsonb('server_actions')->nullable(true);
            $table->longText('success_body')->nullable(true);
            $table->jsonb('close_button_css')->nullable(true);
            $table->jsonb('window_options')->nullable(true);
            $table->jsonb('exclude_urls')->nullable(true);
            $table->json('exclude_pages')->nullable(true);
            $table->boolean('exclude_newsletter_subs')->default(false);
            $table->boolean('exclude_regged_users')->default(false);
            $table->integer('cookie_day_life')->default(30)->nullable(false);
            $table->boolean('is_inactive')->default(false);
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
        Schema::dropIfExists('popups');
    }
}
