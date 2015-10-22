<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTweetTable extends Migration
{
    /**
     * Run the migrations, create table `tweet`
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tweet', function (Blueprint $table) {
            $table->string('id', 32);
            $table->text('content');
            $table->string('username');
            $table->string('user_display_name');
            $table->string('user_avatar');
            $table->string('latitude');
            $table->string('longitude');
            $table->timestamp('fetched_at');
            $table->integer('city_id', false, true);
            $table->timestamps();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations, drop table `tweet`
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tweet');
    }
}
