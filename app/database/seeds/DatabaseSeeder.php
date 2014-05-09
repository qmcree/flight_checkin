<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// $this->call('UserTableSeeder');

        // @see https://php.net/manual/en/timezones.america.php#114172
        Timezone::create(array( 'name' => 'America/New_York', ));
        Timezone::create(array( 'name' => 'America/Chicago', ));
        Timezone::create(array( 'name' => 'America/Denver', ));
        Timezone::create(array( 'name' => 'America/Phoenix', ));
        Timezone::create(array( 'name' => 'America/Los_Angeles', ));
        Timezone::create(array( 'name' => 'America/Anchorage', ));
        Timezone::create(array( 'name' => 'America/Adak', ));
        Timezone::create(array( 'name' => 'Pacific/Honolulu', ));

        // Dates will be converted to UTC (based timezone offset) and inserted. When retrieved, they will be converted back (based on timezone offset).
        Reservation::create(array(
            'confirmation_number' => 'A1B2C3',
            'first_name' => 'Quentin',
            'last_name' => 'McRee',
        ));
        Reservation::create(array(
            'confirmation_number' => 'F9A8B7',
            'first_name' => 'Laura',
            'last_name' => 'Blocker',
        ));
        Reservation::create(array(
            'confirmation_number' => 'D1A3B0',
            'first_name' => 'Ryan',
            'last_name' => 'Rumfelt',
        ));

        Flight::create(array(
            'reservation_id' => 1,
            'date' => '2014-05-02 13:30:00',
            'timezone_id' => 1,
        ));
        Flight::create(array(
            'reservation_id' => 2,
            'date' => '2014-05-02 17:30:00',
            'timezone_id' => 2,
        ));
        Flight::create(array(
            'reservation_id' => 3,
            'date' => '2014-05-02 20:15:00',
            'timezone_id' => 3,
        ));

        Checkin::create(array(
            'reservation_id' => 1,
            'checked_in' => 0,
            'attempts' => 2,
        ));
        Checkin::create(array(
            'reservation_id' => 2,
            'checked_in' => 0,
            'attempts' => 10,
        ));
        Checkin::create(array(
            'reservation_id' => 3,
            'checked_in' => 1,
            'attempts' => 1,
        ));

        CheckinNotice::create(array(
            'reservation_id' => 1,
            'email' => 'develgeek@gmail.com',
        ));
        CheckinNotice::create(array(
            'reservation_id' => 2,
            'email' => 'foobar@localhost',
        ));
        CheckinNotice::create(array(
            'reservation_id' => 3,
            'email' => 'baztest@localhost',
        ));

        $this->command->info('All tables seeded!');
	}

}
