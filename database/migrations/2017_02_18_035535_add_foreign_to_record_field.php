<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignToRecordField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('record_fields', function(Blueprint $table) {
            $table->foreign('record_type_id')->references('id')->on('record_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('record_fields', function(Blueprint $table) {
            $table->dropForeign(['record_type_id']);
        });
    }
}
