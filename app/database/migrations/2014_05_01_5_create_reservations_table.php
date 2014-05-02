<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reservations', function($table) {
            $table->increments('id');
            $table->integer('flight_id')->unsigned();
            $table->string('confirmation_number', 20);
            $table->string('first_name', 20);
            $table->string('last_name', 20);
            $table->timestamps();
        });

        Schema::table('reservations', function($table) {
            $table->foreign('flight_id')->references('id')->on('flights');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reservations');
	}

}