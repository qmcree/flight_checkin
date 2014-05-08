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
		Schema::create('flights', function($table) {
            $table->increments('id');
            $table->dateTime('date'); // @todo add note that this is stored in UTC.
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
