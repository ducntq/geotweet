<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastFetchedToCityTable extends Migration
{
    /**
     * Run the migrations, add column `fetched_at` to table `city`
     *
     * @return void
     */
    public function up()
    {
        Schema::table('city', function (Blueprint $table) {
            $table->timestamp('fetched_at');
        });
    }

    /**
     * Reverse the migrations, remove colum `fetched_at` from table `city`
     *
     * @return void
     */
    public function down()
    {
        Schema::table('city', function (Blueprint $table) {
            $table->removeColumn('fetched_at');
        });
    }
}
