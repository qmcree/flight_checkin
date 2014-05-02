<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAirportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('airports', function($table) {
            $table->increments('id');
            $table->string('abbreviation', 3);
            $table->string('name', 50);
            $table->integer('timezone_id')->unsigned();
        });

        Schema::table('airports', function($table) {
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
		Schema::drop('airports');
	}

}
