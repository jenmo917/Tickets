<?php
class Admin_Model_AdminEvents
{
	/**
	 * Instance of Attend_Db_Table_Events
	 * @var Attend_Db_Table_Events $_eventsTable
	 */
	protected $_eventsTable;

	/**
	 * Instance of Attend_Db_Table_TicketTypes
	 * @var Attend_Db_Table_TicketTypes $_ticketTypeTable
	 */
	protected $_ticketTypeTable;

	/**
	 * Instance of Attend_Db_Table_Tickets
	 * @var Attend_Db_Table_Tickets $_ticketTable
	 */
	protected $_ticketTable;

	protected $_defaultCategories = array('eventCreator' => 'event-creator',);

	protected $_eventInfoForm;

	const VALID					= 'valid';
	const NOT_VALID				= 'notValid';
	const EVENT_CREATED			= 'eventCreated';
	const EVENT_SAVED			= 'eventSaved';
	const EVENT_DELETED			= 'eventDeleted';
	const TICKET_TYPE_ADDED		= 'ticketTypeAdded';
	const TICKET_TYPE_REMOVED	= 'ticketTypeDeleted';
	const FROM_FORM				= 'fromForm';
	const FROM_DB				= 'fromDb';

	public function __construct()
	{
		$this->_eventInfoForm = new Admin_Form_EventInfo();
	}


	public function getEventInfoForm()
	{
		return $this->_eventInfoForm;
	}

	/**
	 * Set Events table.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Attend_Db_Table_Events
	 */
	public function setTicketTable($dbTable)
	{
		if (is_string($dbTable))
		{
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Attend_Db_Table_Tickets)
		{
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_ticketTable = $dbTable;
		return $this;
	}

	/**
	 * Set or get Events table.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Attend_Db_Table_Events
	 */
	public function getTicketTable()
	{
		if (null === $this->_ticketTable)
		{
			$this->setTicketTable('Attend_Db_Table_Tickets');
		}
		return $this->_ticketTable;
	}

	/**
	 * Set Events table.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Attend_Db_Table_Events
	 */
	public function setEventsTable($dbTable)
	{
		if (is_string($dbTable))
		{
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Attend_Db_Table_Events)
		{
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_eventsTable = $dbTable;
		return $this;
	}

	/**
	 * Set or get Events table.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Attend_Db_Table_Events
	 */
	public function getEventsTable()
	{
		if (null === $this->_eventsTable)
		{
			$this->setEventsTable('Attend_Db_Table_Events');
		}
		return $this->_eventsTable;
	}

	/**
	 * Set Tickettypes table.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Attend_Db_Table_TicketTypes
	 */
	public function setTicketTypesTable($dbTable)
	{
		if (is_string($dbTable))
		{
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Attend_Db_Table_TicketTypes)
		{
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_ticketTypeTable = $dbTable;
		return $this;
	}

	/**
	 * Set or get ticket type table.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Attend_Db_Table_TicketTypes
	 */
	public function getTicketTypesTable()
	{
		if (null === $this->_ticketTypeTable)
		{
			$this->setTicketTypesTable('Attend_Db_Table_TicketTypes');
		}
		return $this->_ticketTypeTable;
	}

	public function createEvent(array $formData)
	{
		$rowArray = $this->saveEvent($formData);

		// Add event creator privileges to user.
		$userIdColName				= Acl_Db_Table_Row_Privilege::getColumnName('userId');
		$eventIdPrivilegeColName	= Acl_Db_Table_Row_Privilege::getColumnName('eventId');
		$eventIdEventColName		= Attend_Db_Table_Row_Event::getColumnName('eventId');
		$uI = new Login_Model_UserInfoSession();
		$userId = $uI->getUserId();
		$privilegeSettings = array(	$userIdColName				=> $userId,
									$eventIdPrivilegeColName	=> $rowArray[$eventIdEventColName]);
		Acl_Factory::addDefaultPrivileges($privilegeSettings, $this->_defaultCategories['eventCreator']);

		return $rowArray;
	}

	/**
	 * Save event.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	array of Attend_Db_Table_Row_Event
	 */
	public function saveEvent(&$formData)
	{
		// Get form element names.
		$step1Name = Admin_Form_EventInfo::STEP_1;
		$step2Name = Admin_Form_EventInfo::STEP_2;
		$step3Name = Admin_Form_EventInfo::STEP_3;

		$publicEventForm	= Attend_Db_Table_Row_Event::getColumnNameForUrl('public', '_');
		$eventIdColName		= Attend_Db_Table_Row_Event::getColumnName('eventId');
		$eventIdFormName	= Attend_Db_Table_Row_Event::getColumnNameForUrl('eventId', '_');

		$nameTicketType			= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('name', '_');
		$eventIdTicketType		= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('eventId', '_');
		$ticketTypeIdTicketType	= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('ticketTypeId', '_');

		// Fix params (public is saved with the rest of the event info from step 1)
		$eventData = $formData[$step1Name];
		$eventData[$publicEventForm] = $formData[$step3Name][$publicEventForm];
		$ticketTypesData = &$formData[$step2Name];

		// Save the event.
		$userInfoSession = new Login_Model_UserInfoSession();
		$userId = $userInfoSession->getUserId();
		$this->getEventsTable();

		if(	isset($eventData[$eventIdFormName]) &&
			is_string($eventData[$eventIdFormName]) &&
			strcmp($eventData[$eventIdFormName], ''))
		{
			$row = $this->_eventsTable->fetchRow(
				$this->_eventsTable->select()->where(	$eventIdColName.' = ?',
														$eventData[$eventIdFormName]));
		}
		else
			$row = $this->_eventsTable->createRow();

		$row->setColumnsFromUrl($eventData, '_')->save();

		// Save ticket types.
		// Reset order.
		$orderForm = Attend_Db_Table_Row_TicketType::getColumnNameForUrl('order', '_');
		$order = 1;
		foreach ($ticketTypesData as $key => $ticketType)
		{
		// Save it if name is != ''
			if($ticketType[$nameTicketType] != '')
			{
				// Set event id
				$ticketType[$eventIdTicketType] = $row[$eventIdColName];
				// reset order
				$ticketType[$orderForm] = $order;
				$order++;
				// Save ticket type
				$this->saveTicketType($ticketType);
			}
			// If no name is set and a ticket id is set, the ticket is removed.
			elseif (isset($ticketType[$ticketTypeIdTicketType]))
			{
				$this->removeTicketType(array( $key => $ticketType), $ticketType[$eventIdTicketType]);
				unset($ticketTypesData[$key]);
			}
		}
		return $row->toArray();
	}

	/**
	 * Delete event and ticket types connected to it.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	NULL
	 */
	public function deleteEvent($eventId)
	{
		$this->getEventsTable();
		$row = $this->_eventsTable->fetchRow($this->_eventsTable->select()->where('event_id = ?', $eventId));
		$row->delete();

		$this->getTicketTypesTable();
		$where = $this->_ticketTypeTable->getAdapter()->quoteInto('event_id = ?', $eventId);
		$this->_ticketTypeTable->delete($where);

	}

	/**
	 * Publish or unpublish event
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Attend_Db_Table_Row_Event
	 */
	public function publishEvent($eventId)
	{
		$this->getEventsTable();
		$row = $this->_eventsTable->fetchRow($this->_eventsTable->select()->where('event_id = ?', $eventId));

		if($row->published)
		{
			$row->published = 0;
		}
		else
		{
			$row->published = 1;
		}
		$row->save();
		return $row;
	}

	/**
	 * Fetch events .
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Array with Attend_Db_Table_Row_Event
	 */
	public function fetchEvents($eventIds = array())
	{
		if (!is_array($eventIds))
			throw new Zend_Exception('$eventIds must be an array.');
		$this->getEventsTable();
		if ( empty($eventIds) )
		{
			$rowSet = $this->_eventsTable->fetchAll();
			return ($rowSet instanceof Zend_Db_Table_Rowset_Abstract)? $rowSet->toArray(): null;
		}
		else
		{
			$select = $this->_eventsTable->select();
			$eventIdColName = Attend_Db_Table_Row_Event::getColumnName('eventId');
			foreach ($eventIds as $eventId)
			{
				$select->orWhere($eventIdColName.' = ?', $eventId);
			}
			$rowSet = $this->_eventsTable->fetchAll($select);
			return ($rowSet instanceof Zend_Db_Table_Rowset_Abstract)? $rowSet->toArray(): null;
		}


	}

	/**
	 * Return one event with specific event-id.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	array of Attend_Db_Table_Row_Event
	 */
	public function getEvent($eventId)
	{
		$this->getEventsTable();
		$select = $this->_eventsTable->select()
				->where($this->_eventsTable->getColumnName('eventId').' = ?', $eventId);
		$row = $this->_eventsTable->fetchRow($select);

		return ($row instanceof Attend_Db_Table_Row_Event)? $row->toArray(): null;
	}

	/**
	 * Fetch event attendees.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Array with Attend_Db_Table_Row_Event
	 */
	public function fetchAttendees($eventId)
	{
		$this->getTicketTable();
		// Table and column names
		$ticketTn = $this->_ticketTable->getTableName();
		$ticketName = $this->_ticketTable->getColumnName('name');
		$ticketTicketTypeId = $this->_ticketTable->getColumnName('ticketTypeId');
		$ticketEventId = $this->_ticketTable->getColumnName('eventId');
		$ttTn = Attend_Db_Table_TicketTypes::getTableName();
		$ttTicketTypeId = Attend_Db_Table_TicketTypes::getColumnName('ticketTypeId');
		$ttName = Attend_Db_Table_TicketTypes::getColumnName('name');

		$select = $this->_ticketTable->select();
		$select->setIntegrityCheck(false)
		->from($ticketTn,array('*', 'attendee_name' => $ticketTn.'.'.$ticketName))
		->join($ttTn,$ticketTn.'.'.$ticketTicketTypeId.' = '.$ttTn.'.'.$ttTicketTypeId,
				array('ticket_type_name' => $ttTn.'.'.$ttName))
		->where($ticketTn.'.'.$ticketEventId.' = ?', $eventId)->order('order');
		return $this->_ticketTable->fetchAll($select);
	}

	/**
	 * Save ticket type.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	array of Attend_Db_Table_Row_TicketType
	 */
	public function saveTicketType($ticketType)
	{
		$ticketTypeIdColName		= Attend_Db_Table_Row_TicketType::getColumnName('ticketTypeId');
		$ticketTypeIdFormName		= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('ticketTypeId', '_');
		$this->_ticketTypeTable = new Attend_Db_Table_TicketTypes();
		if(isset($ticketType[$ticketTypeIdFormName]) && $ticketType[$ticketTypeIdFormName] != '')
		{
			$row = $this->_ticketTypeTable->fetchRow(
				$this->_ticketTypeTable->select()->where($ticketTypeIdColName.' = ?', $ticketType[$ticketTypeIdFormName]));
		}
		else
		{
			$row = $this->_ticketTypeTable->createRow();
		}

		$row->setColumnsFromUrl($ticketType, '_');
		$row->save();
		return $row->toArray();
	}

	/**
	 * Return all ticket types (in order) with specific ticket-type-id.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Attend_Db_Table_Row_TicketType
	 */
	public function getTicketTypes($eventId)
	{
		$this->getTicketTypesTable();
		// Table and column names
		$ttTn = $this->_ticketTypeTable->getTableName();
		$ttTicketTypeId = $this->_ticketTypeTable->getColumnName('ticketTypeId');
		$ttEventId = $this->_ticketTypeTable->getColumnName('eventId');
		$ttOrder = $this->_ticketTypeTable->getColumnName('order');

		$tTn = Attend_Db_Table_Tickets::getTableName();
		$tTicketTypeId = Attend_Db_Table_Tickets::getColumnName('ticketTypeId');
		$select = $this->_ticketTypeTable->select();
		$select->setIntegrityCheck(false)
		->from(	$ttTn,
				array(	'*',
						'sold_tickets' => 'COUNT('.$tTn.'.'.$tTicketTypeId.')'))
		->where($ttTn.'.'.$ttEventId.' = ?', $eventId)
		->joinLeft(	$tTn,
					$tTn.'.'.$tTicketTypeId.' = '.$ttTn.'.'.$ttTicketTypeId,
					array())
		->group($ttTn.'.'.$ttTicketTypeId)
		->order($ttTn.'.'.$ttOrder);
		$rowSet = $this->_ticketTypeTable->fetchAll($select);
		return ($rowSet instanceof Zend_Db_Table_Rowset_Abstract)? $rowSet->toArray(): null;
	}

	/**
	 * Return one ticket type with specific ticket-type-id.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Attend_Db_Table_Row_TicketType
	 */
	public function getTicketType($ticketTypeId)
	{
		$this->getTicketTypesTable();
		$select = $this->_ticketTypeTable->select();
		$select->where('ticket_type_id = ?', $ticketTypeId);
		return $this->_ticketTypeTable->fetchRow($select);
	}

	/**
	 * Delete ticket type with specific ticket-type-id.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Number of rows deleted
	 */
	public function deleteTicketType($ticketTypeId)
	{
		$this->getTicketTypesTable();
		$ticketTypeIdCol = $this->_ticketTypeTable->getColumnName('ticketTypeId');
		$this->_ticketTypeTable->delete($this->_ticketTypeTable->getAdapter()->quoteInto($ticketTypeIdCol.' = ?', $ticketTypeId));
	}

	/**
	 * Save ticket.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	array from Attend_Db_Table_Row_Ticket
	 */
	public function saveTicket($ticket)
	{
		$this->getTicketTable();
		$ticketIdCol = Attend_Db_Table_Row_Ticket::getColumnName('ticketId');
		$ticketIdForm = Attend_Db_Table_Row_Ticket::getColumnNameForUrl('ticketId', '_');


		if(isset($ticket[$formNames[$ticketIdForm]]))
		{
			$row = $this->_ticketTable->fetchRow($this->_ticketTable->select()
			->where($ticketIdCol. ' = ?', $ticket[$formNames[$ticketIdForm]]));
		}
		else
			$row = $this->_ticketTable->createRow();

		$row->setColumnsFromUrl($ticket, '_');

		$row->save();
		return $row->toArray();
	}

	public function addTicketTypeSubForms($numTicketTypes)
	{
		for($i = 0; $i < $numTicketTypes; $i++)
		{
			$this->_eventInfoForm->addTicketType();
		}
	}

	public function submittedTicketTypeInfo($ticketTypesSubFormData)
	{
		$numTicketTypes = 0;
		$removeTicketTypeSubmitSet = false;
		$ticketTypeRemoveSubmit = Admin_Form_SubForm_TicketType::REMOVE_TICKET_TYPE_SUBMIT;

		if ( isset($ticketTypesSubFormData) )
		{
			for ( reset($ticketTypesSubFormData); !is_null($key = key($ticketTypesSubFormData)); next($ticketTypesSubFormData) )
			{
				if ( is_int($key) )
				{
					$numTicketTypes++;
					if ( isset($ticketTypesSubFormData[$key][$ticketTypeRemoveSubmit]) )
						$removeTicketTypeSubmitSet = $key;
				}
			}
		}
		return array($numTicketTypes, $removeTicketTypeSubmitSet);
	}

	public function removeTicketType($subFormData, $eventId)
	{
		// Get element names needed.
		$eventIdColName = Attend_Db_Table_Row_TicketType::getColumnName('eventId');
		$ticketTypeIdColName = Attend_Db_Table_Row_TicketType::getColumnName('ticketTypeId');
		$eventIdFormName = Attend_Db_Table_Row_TicketType::getColumnNameForUrl('eventId', '_');

		// Split the input data to key value and get event id.
		$formValues = current($subFormData);
		$eventIdFormValue = $formValues[$eventIdFormName];
		$subFormIndex = key($subFormData);
		// Make sure that the event id value is not altered.
		if ( 	!is_null($eventIdFormValue) && is_string($eventIdFormValue) &&
				strcmp($eventIdFormValue, '') && strcmp($eventIdFormValue, $eventId) )
			throw new Zend_Exception("Given event ids are not consistent. ".$eventIdFormValue.'.'. $eventId);

		// Delete ticket type from database if it exists.
		$this->getTicketTypesTable();
		$ticketTypeId = $formValues[$ticketTypeIdColName];
		$row = null;
		// Make sure it exists.
		if ( 	is_string($ticketTypeId) && strcmp($ticketTypeId, ''))
		{
			$select = $this->_ticketTypeTable->select()
						->where($eventIdColName		.' = ?', $eventId)
						->where($ticketTypeIdColName.' = ?', $ticketTypeId);
			$row = $this->_ticketTypeTable->fetchRow($select);
		}
		// If it did exist, delete it.
		if ( $row instanceof Attend_Db_Table_Row_TicketType )
			$row->delete();

		// Remove from form.
		return $this->_eventInfoForm->removeTicketType($subFormIndex);
	}

	public function handleFormData($formData)
	{
		// Return array
		$return = array();
		// Get form elements names.
		$step1Name = Admin_Form_EventInfo::STEP_1;
		$step2Name = Admin_Form_EventInfo::STEP_2;
		$step3Name = Admin_Form_EventInfo::STEP_3;

		// Action buttons.
		$eventSaveSubmit = Admin_Form_EventInfo::SAVE_EVENT_SUBMIT;
		$ticketTypeNewSubmit = Admin_Form_EventInfo::NEW_TICKET_TYPE_SUBMIT;
		// The $ticketTypeRemoveSubmit exists on several places and are nested in sub forms, following variable
		// is used to tell wether such a button is pressed.

		// Form element names needed.
		$formEventEventId = Attend_Db_Table_Row_Event::getColumnNameForUrl('eventId', '_');

		// Get number of ticket types submitted and decide whether remove ticket type submit is set.
		if ( isset($formData[$step2Name]) )
		{
			list($numTicketTypes, $removeTicketTypeSubmitSet)
			= $this->submittedTicketTypeInfo($formData[$step2Name]);
		}
		else
		{
			$removeTicketTypeSubmitSet = false;
			$numTicketTypes = 0;
		}

		// If neccesary, add ticket type subforms.
		if (1 < $numTicketTypes)
		$this->addTicketTypeSubForms($numTicketTypes - 1);

		// User has tried to save the event.
		if ( isset($formData[$eventSaveSubmit]) )
		{
			// Make sure that the input is valid.
			if ( $this->_eventInfoForm->isValid($formData) )
			{
				$return[] = self::VALID;
				$eventCols = Attend_Db_Table_Row_Event::getColumnNames('both');
				// If no event-id is set, this is a new event and it should be created.
				if ( 	!isset($formData[$step1Name][$formEventEventId]) ||
						is_string($formData[$step1Name][$formEventEventId]) &&
						!strcmp($formData[$step1Name][$formEventEventId], '') )
				{
					$return[] = self::EVENT_CREATED;
					$event = $this->createEvent($formData);
					$return['eventName'] = $event[$eventCols['name']];
				}
				else
				{
					$return[] = self::EVENT_SAVED;
					$event = $this->saveEvent($formData);
					$return['eventName'] = $event[$eventCols['name']];
				}
			}
			else
			{
				$return[] = self::NOT_VALID;
			}
		}
		elseif (isset($formData[$step2Name][$ticketTypeNewSubmit]))
		{
			$this->addTicketTypeSubForms(1);
			$return[] = self::TICKET_TYPE_ADDED;
		}
		elseif (false !== $removeTicketTypeSubmitSet)
		{
			$remove = array($removeTicketTypeSubmitSet => $formData[$step2Name][$removeTicketTypeSubmitSet]);
			$this->removeTicketType($remove, $formData[$step1Name][$formEventEventId]);
			$return[] = self::TICKET_TYPE_REMOVED;

		}

		// Populate form with data from post if available and from db if event id is set in url.
		if ( 	isset($formData[$eventSaveSubmit]) ||
				isset($formData[$step2Name][$ticketTypeNewSubmit]) ||
				true === $removeTicketTypeSubmitSet)
		{
			$return[] = self::FROM_FORM;
			$this->_eventInfoForm->populate($formData);
		}
		elseif (isset($formData[$step1Name][$formEventEventId])) //
		{
			$return[] = self::FROM_DB;
			// Set Event id when editing.
			$eventId = $formData[$step1Name][$formEventEventId];
			$event = $this->getEvent($eventId);
			$this->_eventInfoForm->setEventId($eventId);
			// Form isnt submitted so vi have to populate with data from the database
			$eventForm = Attend_Db_Table_Row_Event::getColumnNames('both', '_');
			$eventCols = Attend_Db_Table_Row_Event::getColumnNames('both');
			// Form step 1
			foreach (array_keys($eventForm) as $key)
			{
				$vars[$step1Name][$eventForm[$key]] = $event[$eventCols[$key]];
			}

			// Form step 2
			$ticketTypes = $this->getTicketTypes($eventId);
			$vars[$step2Name] = array();
			$ticketTypeForm = Attend_Db_Table_Row_TicketType::getColumnNames('both', '_');
			$ticketTypeCols = Attend_Db_Table_Row_TicketType::getColumnNames('both');
			foreach ($ticketTypes as $ticketType)
			{
				$data = array();
				foreach (array_keys($ticketTypeForm) as $key)
				{
					$data[$ticketTypeForm[$key]] = $ticketType[$ticketTypeCols[$key]];
				}
				$vars[$step2Name][] = $data;
			}

			// Form step 3
			$vars[$step3Name] = array(
			$eventForm['public'] => $event[$eventCols['public']]
			);
			$this->_eventInfoForm->populate($vars);
		}
		return $return;
	}
}
