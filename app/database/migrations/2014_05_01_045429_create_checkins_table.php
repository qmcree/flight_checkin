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
            $table->integer('reservation_id')->unsigned();
            $table->string('passenger_email', 30);
            $table->tinyInteger('checked_in')->unsigned();
            $table->tinyInteger('attempts')->unsigned();
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
