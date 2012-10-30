<?php

class Default_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $eventModel = new Default_Model_Events();
        $events = $eventModel->fetchFrontPageEvents();
        $this->view->events = $events;
    }

	 /**
	 * Overview of a specific event
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function overviewAction()
	{
		// Fetch event
		$eventIdColName = Attend_Db_Table_Row_Event::getColumnNameForUrl('eventId');
		$eventName = 'eventname';
		$events = new Default_Model_Events();
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$params = $this->getRequest()->getParams();
		// Check after event id
		if (isset($params[$eventIdColName]))
		{
			$event = $events->getEvent($params[$eventIdColName]);
		}
		// Check after event name
		elseif (isset($params[$eventName]))
		{
			$event = $events->getEventByName($params[$eventName]);
		}
		else
		{
			$event = array();
		}

		$this->view->event = $event;
		$this->view->messages = $flashMessenger->getMessages();
	}


	/**
	 *
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return
	 */
	public function validateTicketFormAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		$f = new Admin_Form_SellTickets();
		$f->isValid($this->_getAllParams());
		$json = $f->getMessages();

		// Tell the browser that we are sending some json data
		header('content-type: application/json');
		echo Zend_Json::encode($json);
	}
	
	public function testAction()
	{
		$f = new Admin_Form_SellTickets();
		$vars = array(
			'ticket_type_id' => ''
		);
		$f->isValid($vars);
		var_dump($f);
	}
}

