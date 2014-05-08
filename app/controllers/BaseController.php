<?php

class BaseController extends Controller {

    private $alert = array(
        'success' => null,
        'info' => null,
        'warning' => null,
        'danger' => null,
    );

    protected function setAlertSuccess($alert)
    {
        $this->alert['success'] = $alert;
    }

    protected function setAlertInfo($alert)
    {
        $this->alert['info'] = $alert;
    }

    protected function setAlertWarning($alert)
    {
        $this->alert['warning'] = $alert;
    }

    protected function setAlertDanger($alert)
    {
        $this->alert['danger'] = $alert;
    }

    private function getAlertSuccess()
    {
        return $this->alert['success'];
    }

    private function getAlertInfo()
    {
        return $this->alert['info'];
    }

    private function getAlertWarning()
    {
        return $this->alert['warning'];
    }

    private function getAlertDanger()
    {
        return $this->alert['danger'];
    }

    /**
     * Makes specified view.
     * @param string $name Name of the view.
     * @param array [$params] Additional params to pass to the view.
     * @return View
     */
    public function makeView($name, $params = array())
    {
        $base = array(
            '_success' => $this->getAlertSuccess(),
            '_info' => $this->getAlertInfo(),
            '_warning' => $this->getAlertWarning(),
            '_danger' => $this->getAlertDanger(),
            'reservation_count' => Reservation::all()->count(),
        );

        $params = array_merge($base, $params);

        return View::make($name, $params);
    }

    /**
     * Renders messages into HTML.
     * @param Validator $messages
     * @return string
     */
    public static function renderMessages($messages)
    {
        $html = '<ul>';
        foreach ($messages->all('<li>:message</li>') as $message) {
            $html .= $message;
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Converts date, timezone pair to UTC date.
     * @param string $timezoneName
     * @param string $date
     * @return string
     */
    protected static function getUtcDate($timezoneName, $date)
    {
        $timezone = new DateTimeZone($timezoneName);
        $dateTime = new DateTime($date, $timezone);

        return gmdate('Y-m-d H:i:s', $dateTime->getTimestamp());
    }

    /**
     * Converts date, timezone pair to time.
     * @param string $timezoneName
     * @param string $date
     * @return integer UNIX timestamp.
     */
    protected static function getTime($timezoneName, $date)
    {
        $timezone = new DateTimeZone($timezoneName);
        $dateTime = new DateTime($date, $timezone);

        return $dateTime->getTimestamp();
    }

    /**
     * Determines if UTC date is in past.
     * @param string $date in UTC
     * @return boolean
     */
    protected static function hasPassed($date)
    {
        return (self::getTime('UTC', $date) <= time());
    }

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}
