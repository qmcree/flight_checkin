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

    public function getAlertSuccess()
    {
        return $this->alert['success'];
    }

    protected function setAlertInfo($alert)
    {
        $this->alert['info'] = $alert;
    }

    public function getAlertInfo()
    {
        return $this->alert['info'];
    }

    protected function setAlertWarning($alert)
    {
        $this->alert['warning'] = $alert;
    }

    public function getAlertWarning()
    {
        return $this->alert['warning'];
    }

    protected function setAlertDanger($alert)
    {
        $this->alert['danger'] = $alert;
    }

    public function getAlertDanger()
    {
        return $this->alert['danger'];
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
     * Converts date to UTC based on timezone.
     * @param integer $timezoneId
     * @param string $date
     * @return string
     */
    protected static function getUtcDate($timezoneId, $date)
    {
        $timezone = Timezone::find($timezoneId);

        $timezone = new DateTimeZone($timezone['attributes']['name']);
        $dateTime = new DateTime($date, $timezone);

        return gmdate('Y-m-d H:i:s', $dateTime->getTimestamp());
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
