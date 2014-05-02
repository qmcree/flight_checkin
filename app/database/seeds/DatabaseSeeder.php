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

        Airline::create(array( 'name' => 'Southwest Airlines', ));

        // timezone_id assumes timezones are inserted in order above.
        Airport::create(array(
            'abbreviation' => 'ATL',
            'name' => 'Hartsfield-Jackson Atlanta International Airport',
            'timezone_id' => 1,
        ));
        Airport::create(array(
            'abbreviation' => 'JAN',
            'name' => 'Jackson-Evers International Airport',
            'timezone_id' => 2,
        ));
        Airport::create(array(
            'abbreviation' => 'DEN',
            'name' => 'Denver International Airport',
            'timezone_id' => 3,
        ));
        Airport::create(array(
            'abbreviation' => 'PHX',
            'name' => 'Phoenix Sky Harbor International Airport',
            'timezone_id' => 4,
        ));
        Airport::create(array(
            'abbreviation' => 'LAX',
            'name' => 'Los Angeles International Airport',
            'timezone_id' => 5,
        ));

        // Dates will be converted to UTC (based on airport's timezone offset) and inserted. When retrieved, they will be converted back (based on airport's timezone offset).
        Flight::create(array(
            'date' => '2014-05-02 13:30:00',
            'airline_id' => 1,
            'airport_id' => 1,
        ));
        Flight::create(array(
            'date' => '2014-05-02 17:30:00',
            'airline_id' => 1,
            'airport_id' =>2 ,
        ));
        Flight::create(array(
            'date' => '2014-05-02 20:15:00',
            'airline_id' => 1,
            'airport_id' => 3,
        ));

        Reservation::create(array(
            'flight_id' => 1,
            'confirmation_number' => 'A1B2C3',
            'first_name' => 'Quentin',
            'last_name' => 'McRee',
        ));
        Reservation::create(array(
            'flight_id' => 2,
            'confirmation_number' => 'F9A8B7',
            'first_name' => 'Laura',
            'last_name' => 'Blocker',
        ));
        Reservation::create(array(
            'flight_id' => 3,
            'confirmation_number' => 'D1A3B0',
            'first_name' => 'Ryan',
            'last_name' => 'Rumfelt',
        ));

        Checkin::create(array(
            'reservation_id' => 1,
            'passenger_email' => 'develgeek@gmail.com',
            'checked_in' => 0,
            'attempts' => 2,
        ));
        Checkin::create(array(
            'reservation_id' => 2,
            'passenger_email' => 'foobar@localhost',
            'checked_in' => 0,
            'attempts' => 10,
        ));
        Checkin::create(array(
            'reservation_id' => 1,
            'passenger_email' => 'baztest@localhost',
            'checked_in' => 1,
            'attempts' => 1,
        ));
	}

}
