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
            $table->dateTime('date');
            $table->integer('airline_id')->unsigned();
            $table->integer('airport_id')->unsigned();
        });

        Schema::table('flights', function($table) {
            $table->foreign('airline_id')->references('id')->on('airlines');
            $table->foreign('airport_id')->references('id')->on('airports');
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
