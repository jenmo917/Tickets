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
		$events = new Default_Model_Events();
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$params = $this->getRequest()->getParams();
		$event = $events->getEvent($params[$eventIdColName]);
		$this->view->event = $event;
		$this->view->messages = $flashMessenger->getMessages();
	}


}

