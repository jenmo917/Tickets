<?php

class Admin_EventController extends Zend_Controller_Action
{

	/**
	 * Overview of a specific event
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function indexAction()
	{
		// Fetch event
		$eventIdColName = Attend_Db_Table_Row_Event::getColumnNameForUrl('eventId');
		$events = new Admin_Model_AdminEvents();
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$params = $this->getRequest()->getParams();
		$event = $events->getEvent($params[$eventIdColName]);
		$this->view->event = $event;
		$this->view->messages = $flashMessenger->getMessages();
	}

	/**
	 * Create new event
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function createEventAction()
	{
		// Initiate vars/objects.
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');

		// $translate is important for translation to work
		$translate = Zend_Registry::get('Zend_Translate');

		//Get form elements names.
		$step1Name = Admin_Form_EventInfo::STEP_1;

		// Set up data to send to admin events
		$formData = $this->getRequest()->getPost();

		// Initiate model
		$events = new Admin_Model_AdminEvents();
		$status = $events->handleFormData($formData);

		// Send form to view
		$this->view->form = $events->getEventInfoForm();

		// If created event is in the status array. Display it for the user.
		if(in_array($events::EVENT_CREATED, $status))
		{
			// Add message
			$translate = Zend_Registry::get('Zend_Translate');
			$flashMessenger = $this->_helper->getHelper('FlashMessenger');
			$flashMessenger->addMessage($status['eventName'].' '.$translate->_('created').'!');
			$this->view->messages = $flashMessenger->getMessages();

			$this->_redirect($this->_helper->url->url(array('module' => 'admin', 'controller' => 'event', 'action' => 'my-events'),null, true));
		}
	}

	/**
	 * Get attendees for a specific event.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function attendeesAction()
	{
		// Fetch event
		$eventIdColName = Attend_Db_Table_Row_Event::getColumnNameForUrl('eventId');
		$events = new Admin_Model_AdminEvents();
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$params = $this->getRequest()->getParams();
		$event = $events->getEvent($params[$eventIdColName]);
		$this->view->event = $event;

		// Fetch attendees
		$attendees = $events->fetchAttendees($params[$eventIdColName])->toArray();
		$this->view->attendees = $attendees;
	}

	/**
	 * Sell tickets in a specific event
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function sellAction()
	{
		// Element names needed.
		$eventIdColName = Attend_Db_Table_Row_Event::getColumnNameForUrl('eventId');
		$ticketTypeFormNames = Attend_Db_Table_Row_TicketType::getColumnNames('both', '_');
		$ticketTypeColNames = Attend_Db_Table_Row_TicketType::getColumnNames('both');
		//add soldTickets to above arrays, this is a special comeing from getTicketTypes.
		$ticketTypeFormNames['soldTickets'] = 'sold_tickets';
		$ticketTypeColNames['soldTickets'] = 'sold_tickets';

		$events = new Admin_Model_AdminEvents();
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$params = $this->getRequest()->getParams();
		$event = $events->getEvent($params[$eventIdColName]);
		$this->view->event = $event;
		$this->view->messages = $flashMessenger->getMessages();

		// $translate is important for translation to work
		$translate = Zend_Registry::get('Zend_Translate');

		// Create form
		$form = new Admin_Form_SellTickets();
		$form->setEventID($params[$eventIdColName]);
		$ticketTypes = $events->getTicketTypes($params[$eventIdColName]);
		$ticketTypesForm = array();
		foreach ($ticketTypes as $ticketType)
		{
			$newTicketType = array();
			foreach (array_keys($ticketTypeColNames) as $key)
			{
				$newTicketType[$ticketTypeFormNames[$key]] = $ticketType[$ticketTypeColNames[$key]];
			};
			$ticketTypesForm[] = $newTicketType;
		}
		$form->create($ticketTypesForm);
		$params = $this->getRequest()->getParams();
		$post = $this->getRequest()->getPost();

		if(isset($post[Admin_Form_SellTickets::REGISTER_TICKET_SUBMIT]) && $form->isValid($params))
		{
			unset($post[Admin_Form_SellTickets::REGISTER_TICKET_SUBMIT]);
			// Initiate Mailer object
			$htmlMailer = new Generic_HtmlMailer();

			// Add event id to post
			$post[$ticketTypeFormNames['eventId']] = $params[$eventIdColName];
			// Save ticket to DB
			$ticket = $events->saveTicket($post);

			// Get ticket price
			$ticketType = $events->getTicketType($params[$ticketTypeFormNames['ticketTypeId']]);

			// If invoice
			if($params['payment'] == 'invoice')
			{
				// generate ocr with luhn algorithm from ticket_id
				$ocrHelper = new Generic_Ocr();
				$ocr = $ocrHelper->luhn($ticket->ticket_id);

				// Send invoice to buyer
				$htmlMailer->setSubject("[TDDD27] ".$translate->_('Invoice'))
				->addTo($params['email'])
				->setViewParam("ocr", $ocr)
				->setViewParam("ticketType", $ticketType->name)
				->setViewParam("name", $params['name'])
				->setViewParam("email", $params['email'])
				->setViewParam('translate', $translate)
				->setViewParam("price", $ticketType->price)
				->sendHtmlTemplate("invoice.phtml");
			}
			else
			{
				// Send payment confirmation to buyer
				$htmlMailer->setSubject("[TDDD27] ".$translate->_('Payment confirmation'))
				->addTo($params['email'])
				->setViewParam("ticketType", $ticketType->name)
				->setViewParam("name", $params['name'])
				->setViewParam("email", $params['email'])
				->setViewParam('translate', $translate)
				->setViewParam("price", $ticketType->price)
				->sendHtmlTemplate("payment-confirmation.phtml");
			}

			// Set message
			$flashMessenger->addMessage($translate->_('Ticket is registred'));

			// Redirect to admin/event/sell
			$this->_redirect($this->_helper->url->url(array('module' => 'admin','controller' => 'event', 'action' => 'sell', $eventIdColName => $params[$eventIdColName]),"defaultRoute",true));
		}
		$this->view->ticketForm = $form;
	}

	/**
	 * Edit specific event
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function editAction()
	{
		// Initiate vars/objects.
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');

		// $translate is important for translation to work
		$translate = Zend_Registry::get('Zend_Translate');

		//Get form elements names.
		$step1Name = Admin_Form_EventInfo::STEP_1;

		// Set up data to send to admin events
		$formData = $this->getRequest()->getPost();

		// Get event id. Form has lower priority due to it can not be validated through RBAC.
		$eventIdColNameUrl = Attend_Db_Table_Row_Event::getColumnNameForUrl('eventId');
		$eventIdColNameForm = Attend_Db_Table_Row_Event::getColumnNameForUrl('eventId', '_');
		$eventIdParam = $this->getRequest()->getParam($eventIdColNameUrl);
		if(	!isset($formData[$step1Name][$eventIdColNameForm]) ||
			( !is_null($eventIdParam) && $formData[$step1Name][$eventIdColNameForm] !== $eventIdParam ) )
		{
			$formData[$step1Name][$eventIdColNameForm] = $eventIdParam;
		}

		// Initiate model
		$events = new Admin_Model_AdminEvents();
		$status = $events->handleFormData($formData);

		// Send form to view
		$this->view->form = $events->getEventInfoForm();

		if(in_array($events::EVENT_SAVED, $status))
		{
			// Add message
			$translate = Zend_Registry::get('Zend_Translate');
			$flashMessenger = $this->_helper->getHelper('FlashMessenger');
			$flashMessenger->addMessage($status['eventName'].' '.$translate->_('saved').'!');
			$this->view->messages = $flashMessenger->getMessages();
		}
	}

	/**
	 * Delete specific event
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function deleteAction()
	{
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		// $translate is important for translation to work
		$translate = Zend_Registry::get('Zend_Translate');

		// Initiate object
		$events = new Admin_Model_AdminEvents();

		// Filter eventId
		$filter = new Zend_Filter_Digits();
		$params = $this->getRequest()->getParams();
		$eventIdColName = Attend_Db_Table_Row_Event::getColumnNameForUrl('eventId');
		if(isset($params[$eventIdColName]))
		{
			$eventId = $filter->filter($params[$eventIdColName]);
		}
		else
		{
			// TODO: What if wrong or none event_id is set
		}

		// Get event for flashMessenger
		$event = $events->getEvent($eventId);

		// Delete event
		$events->deleteEvent($eventId);

		// Set message
		$flashMessenger->addMessage($event[Attend_Db_Table_Row_Event::getColumnName('name')].' '.$translate->_('is now deleted'));

		// Redirect to admin/index
		$this->_redirect($this->_helper->url->url(array('module' => 'admin'),"defaultRoute",true));
	}

	/**
	 * Publish/unpublish event. This will controll if the event is visible on the front page
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	array
	 */
	public function publishAction()
	{
		// Get flashmessenger
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');

		// $translate is important for translation to work
		$translate = Zend_Registry::get('Zend_Translate');

		// Initiate object
		$events = new Admin_Model_AdminEvents();

		// Filter eventId
		$filter = new Zend_Filter_Digits();
		$params = $this->getRequest()->getParams();

		$eventIdColName = Attend_Db_Table_Row_Event::getColumnNameForUrl('eventId');
		if(isset($params[$eventIdColName]))
		{
			$eventId = $filter->filter($params[$eventIdColName]);
		}

		// Get event for flashMessenger
		$event = $events->getEvent($eventId);

		// Publish/Unpublish event
		$events->publishEvent($eventId);

		// Set message
		if($event->published)
		{
			$flashMessenger->addMessage($event->name. ' '.$translate->_('has been unpublished'));
		}
		else
		{
			$flashMessenger->addMessage($event->name. ' '.$translate->_('has been published'));
		}

		// Redirect to admin/index
		$this->_redirect($this->_helper->url->url(array('module' => 'admin', 'controller' => 'event', 'action' => 'my-events'),"defaultRoute",true));
	}

	public function adminAction()
	{
		$eventId = $this->getRequest()->getParam('event-id');
		$adminEvents = new Admin_Model_AdminEvents();
		$this->view->eventInfo = $adminEvents->getEvent($eventId);
	}

	public function myEventsAction()
	{
		$uI = new Login_Model_UserInfoSession();
		$events = new Admin_Model_AdminEvents();
		$eventIds = $uI->getUserEventIds();
		$this->view->events = $events->fetchEvents($eventIds);
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->messages = $flashMessenger->getMessages();
	}
}
