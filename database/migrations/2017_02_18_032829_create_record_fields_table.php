<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('value_type');
            $table->integer('record_type_id')->index()->nullable(true);
            $table->string('formula');
            $table->integer('sort_order')->nullable(true);
            $table->boolean('searchable')->nullable(true);
            $table->integer('search_priority')->nullable(true);
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
        Schema::dropIfExists('record_fields');
    }
}
