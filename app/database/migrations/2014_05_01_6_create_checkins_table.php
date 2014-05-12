<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckinsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('checkins', function($table) {
            $table->integer('reservation_id')->unsigned()->unique();
            $table->tinyInteger('checked_in')->unsigned()->default(0);
            $table->tinyInteger('attempts')->unsigned()->default(0);
        });

        Schema::table('checkins', function($table) {
            $table->foreign('reservation_id')->references('id')->on('reservations');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('checkins');
	}

}
