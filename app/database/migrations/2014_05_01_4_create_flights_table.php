<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlightsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        // date is stored in UTC.
        // timezone_id is used to (1) convert local date to UTC when being saved and (2) convert stored UTC date to local date for UI.
		Schema::create('flights', function($table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->integer('timezone_id')->unsigned();
        });

        Schema::table('flights', function($table) {
            $table->foreign('timezone_id')->references('id')->on('timezones');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('flights');
	}

}
