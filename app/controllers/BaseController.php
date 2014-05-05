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

    protected function getAlertSuccess()
    {
        return $this->alert['success'];
    }

    protected function setAlertInfo($alert)
    {
        $this->alert['info'] = $alert;
    }

    protected function getAlertInfo()
    {
        return $this->alert['info'];
    }

    protected function setAlertWarning($alert)
    {
        $this->alert['warning'] = $alert;
    }

    protected function getAlertWarning()
    {
        return $this->alert['warning'];
    }

    protected function setAlertDanger($alert)
    {
        $this->alert['danger'] = $alert;
    }

    protected function getAlertDanger()
    {
        return $this->alert['danger'];
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
