<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use FlightCheckin\CheckinAction;

class CheckinCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:checkin';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Checks in passenger reservations if within (24) hour window of flight.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $upcomingFlights = Flight::with('reservation.checkin', 'reservation.checkinNotice')->upcoming()->get();

        foreach ($upcomingFlights as $flight) {
            try {
                $checkin = new CheckinAction($flight);
                $checkin->attempt();
            }
            catch (\FlightCheckin\CheckinActionException $e) {
                $this->error($e->getMessage());
            }
        }
	}
}
