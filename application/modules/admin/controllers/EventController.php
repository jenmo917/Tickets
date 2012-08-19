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
		$eventIdColName = Admin_Model_DbTable_Row_Event::getColumnNameForUrl('eventId');
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
		// Create model
		$events = new Admin_Model_AdminEvents();

		// Get data
		$data = $this->_request->getParams();

		// Create form
		$form = new Admin_Form_EventInfo();

		// How many ticket type fieldsets to view in the form
		if(!isset($data['step2']))
		{
			$numOfTicketTypes = 1;
		}
		else
		{
			// Fix array so it starts with [0], [1], [2],..
			// jQuery in the form is the problem.
			$temp = array();
			// Reset order so it starts from 0.
			$i = 0;
			foreach($data['step2'] as $entry):
				$entry['order'] = $i;
				$temp[] = $entry;
				$i++;
			endforeach;
			$data['step2'] = $temp;

			// How many ticket types to loop through?
			$numOfTicketTypes = COUNT($data['step2']);
		}
		// Create form
		$form->create($numOfTicketTypes);

		// Assign form to view
		$this->view->form = $form;

		// If form is valid
		if(isset($data['submit']) && $form->isValid($data))
		{
			// Remove submit from data
			unset($data['submit']);

			// Fix params (public is saved with the rest of the event info from step 1)
			$data['event'] = $data['step1'];
			$data['event']['public'] = $data['step3']['public'];

			// Save event
			$event = $events->createEvent($data['event']);

			// Add message
			$flashMessenger = $this->_helper->getHelper('FlashMessenger');
			$flashMessenger->addMessage($event->name.' skapades!');

			// Save ticket types
			foreach ($data['step2'] as $ticketTypeArray):
				// Save it if name is != ''
				if($ticketTypeArray['name'] != '')
				{
					// Set event_id
					$ticketTypeArray['event_id'] = $event->event_id;
					// Save ticket type
					$events->saveTicketType($ticketTypeArray);
				}
			endforeach;
			$this->_redirect($this->_helper->url->url(array('module' => 'admin'),null, true));
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
		$events = new Admin_Model_AdminEvents();
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$params = $this->getRequest()->getParams();
		$event = $events->getEvent($params['event_id']);
		$this->view->event = $event;

		// Fetch attendees
		$attendees = $events->fetchAttendees($params['event_id'])->toArray();
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
		// Fetch event
		$events = new Admin_Model_AdminEvents();
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$params = $this->getRequest()->getParams();
		$event = $events->getEvent($params['event_id']);
		$this->view->event = $event;
		$this->view->messages = $flashMessenger->getMessages();

		// $translate is important for translation to work
		$translate = Zend_Registry::get('Zend_Translate');

		// Create form
		$form = new Admin_Form_SellTickets();
		$form->setEventID($params['event_id']);
		$form->create();
		$params = $this->getRequest()->getParams();

		if(isset($params['submit']) && $form->isValid($params))
		{
			// Initiate Event model
			$adminEvent = new Admin_Model_AdminEvents();

			// Initiate Mailer object
			$htmlMailer = new Generic_HtmlMailer();

			// Save ticket to DB
			$ticket = $adminEvent->saveTicket($params);

			// Get ticket price
			$ticketType = $adminEvent->getTicketType($params['ticket_type_id']);

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
			$this->_redirect($this->_helper->url->url(array('module' => 'admin','controller' => 'event', 'action' => 'sell', 'event_id' => $params['event_id']),"defaultRoute",true));
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

		// FIX: Get params??
		$post = $this->getRequest()->getPost();
		$get  = $this->getRequest()->getQuery();

		$params = $this->getRequest()->getParams();

		if(isset($params['event_id']))
		{
			$eventId = $params['event_id'];
		}

		// Initiate model
		$events = new Admin_Model_AdminEvents();

		// Fix between post and get vars.
		if(isset($post['event_id']))
		{
			$eventId = $post['event_id'];
		}
		elseif(isset($post['event_id']))
		{
			$eventId = $post['event_id'];
		}
		elseif(isset($get['event_id']))
		{
			$eventId = $get['event_id'];
		}

		// Fetch event
		$event = $events->getEvent($eventId);
		// Fetch ticket types
		$ticketTypes = $events->getTicketTypes($eventId);

		// Create form with correct number of ticket types
		if(isset($post['submit']))
		{
			// Fix array so it starts with [0], [1], [2],..
			// jQuery in the form is the problem.
			$temp = array();
			foreach($post['step2'] as $entry):
			$temp[] = $entry;
			endforeach;
			$post['step2'] = $temp;

			$numOfTicketTypes = COUNT($post['step2']);
		}
		else
		{
			// How many ticket type fieldsets to view in the form
			$numOfTicketTypes = COUNT($ticketTypes);
		}

		// Create form
		$form = new Admin_Form_EventInfo();

		// Create at least one ticket type form
		if($numOfTicketTypes == 0){
			$numOfTicketTypes = 1;
		}
		$form->create($numOfTicketTypes);

		// If form is valid
		if(isset($post['submit']) && $form->isValid($post))
		{
			// Prepare data
			$eventData = $post['step1'];
			$eventData['event_id'] = $eventId;
			$eventData['public']   = $post['step3']['public'];
			$ticketTypeData = $post['step2'];

			// Save event
			$events->saveEvent($eventData);

			// Set message
			$flashMessenger->addMessage($translate->_('Event is now updated'));

			// Save ticket types
			foreach ($ticketTypeData as $ticketTypeArray):

				$ticketTypeArray['event_id'] = $eventId;

				// If ticket type exists and the name isnt set, it will be removed.
				if(isset($ticketTypeArray['ticket_type_id']) && $ticketTypeArray['name'] == '')
				{
					// Delete ticket type
					$events->deleteTicketType($ticketTypeArray['ticket_type_id']);
				}
				else
				{
					// save ticket type
					$events->saveTicketType($ticketTypeArray);
				}
			endforeach;
			// Redirect to admin/index
			$this->_redirect($this->_helper->url->url(array('module' => 'admin'),"defaultRoute",true));
		}

		// Populate form with data.
		if(isset($post['submit']))
		{
			// Form is submitted so we choose the posted data
			$vars = $post;
		}
		else
		{
			// Form isnt submitted so vi have to populate with data from the database

			// Form step 1
			$step1 = array(
				'name'		=> $event->name,
				'location'	=> $event->location,
				'details'	=> $event->details,
				'start_time'=> $event->start_time,
				'end_time'	=> $event->end_time,
			);

			// Form step 2
			$step2 = array();
			// Reset order so it starts from 0.
			$i = 0;
			foreach ($ticketTypes as $ticketType):
			//var_dump($ticketType);
				$data = array(
					'name'				=> $ticketType->name,
					'quantity'			=> $ticketType->quantity,
					'price'				=> $ticketType->price,
					'details'			=> $ticketType->details,
					'ticket_type_id'	=> $ticketType->ticket_type_id,
					'order'				=> $i
				);
				$i++;
				$step2[] = $data;
			endforeach;

			// Form step 3
			$step3 = array(
				'public' => $event->public
			);

			// Form steps
			$vars = array(
				'step1' => $step1,
				'step2' => $step2,
				'step3' => $step3
			);
		}
		// Populate form with data
		$form->populate($vars);

		// Set EventId when edit
		$form->setEventId($eventId);

		// Send form to view
		$this->view->form = $form;
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
		if(isset($params['event_id']))
		{
			$eventId = $filter->filter($params['event_id']);
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
		$flashMessenger->addMessage($event->name.' '.$translate->_('is now deleted'));

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

		if(isset($params['event_id']))
		{
			$eventId = $filter->filter($params['event_id']);
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
		$this->_redirect($this->_helper->url->url(array('module' => 'admin'),"defaultRoute",true));
	}
}

