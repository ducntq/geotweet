<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityTable extends Migration
{
    protected static $tableName = 'city';

    /**
     * Create table "city"
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::$tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('index_name');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('country');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations, drop table "city"
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(self::$tableName);
    }
}
