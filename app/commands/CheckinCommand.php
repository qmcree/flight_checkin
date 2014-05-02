<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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
        date_default_timezone_set('UTC');
        $now = date('Y-m-d H:i:s', time());

		$flights = Flight::where('date', '<', $now);
        $this->info('There are ' . $flights->count() . ' past flights.');
	}
}
