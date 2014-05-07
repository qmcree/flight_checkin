<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckinNoticesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('checkin_notices', function($table) {
            $table->integer('reservation_id')->unsigned();
            $table->string('email', 30);
            $table->timestamp('notified_at')->nullable();
        });

        Schema::table('checkin_notices', function($table) {
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
		Schema::drop('checkin_notices');
	}

}
