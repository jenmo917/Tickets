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

	public function createEvent($event)
	{
		$userInfoSession = new Login_Model_UserInfoSession();
		$userId = $userInfoSession->getUserId();
		$userIdColName	= Acl_Db_Table_Privileges::getColumnName('userId');
		$eventIdColName	= Acl_Db_Table_Privileges::getColumnName('eventId');
		$row = $this->saveEvent($event);
		$privilegeSettings = array(	$userIdColName	=> $userId,
									$eventIdColName	=> $row->getColumn('eventId'));
		Acl_Factory::addDefaultPrivileges($privilegeSettings, $this->_defaultCategories['eventCreator']);
		return $row;
	}

	/**
	 * Save event.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Admin_Model_DbTable_Row_Event
	 */
	public function saveEvent($event)
	{
		$this->getEventsTable();
		if(isset($event['event_id']))
		{
			$row = $this->_eventsTable->fetchRow($this->_eventsTable->select()->where('event_id = ?', $event['event_id']));
		}
		else
		{
			$row = $this->_eventsTable->createRow();
		}
		$row->name			= $event['name'];
		$row->location		= $event['location'];
		$row->details		= $event['details'];
		$row->public		= $event['public'];
		$row->start_time	= $event['start_time'];
		$row->end_time		= $event['end_time'];

		$row->save();
		return $row;
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
			return $this->_eventsTable->fetchAll()->toArray();
		}
		else
		{
			$select = $this->_eventsTable->select();
			$eventIdColName = Attend_Db_Table_Row_Event::getColumnName('eventId');
			foreach ($eventIds as $eventId)
			{
				$select->orWhere($eventIdColName.' = ?', $eventId);
			}
			return $this->_eventsTable->fetchAll($select)->toArray();
		}


	}

	/**
	 * Return one event with specific event-id.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Attend_Db_Table_Row_Event
	 */
	public function getEvent($eventId)
	{
		$this->getEventsTable();
		$select = $this->_eventsTable->select()
				->where($this->_eventsTable->getColumnName('eventId').' = ?', $eventId);
		return $row = $this->_eventsTable->fetchRow($select);
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
	 * @return	Admin_Model_DbTable_Row_TicketType
	 */
	public function saveTicketType($ticketType)
	{
		$this->_ticketTypeTable = new Admin_Model_DbTable_TicketTypes();
		if(isset($ticketType['ticket_type_id']) && $ticketType['ticket_type_id'] != '')
		{
			$row = $this->_ticketTypeTable->fetchRow($this->_ticketTypeTable->select()->where('ticket_type_id = ?', $ticketType['ticket_type_id']));
		}
		else
		{
			$row = $this->_ticketTypeTable->createRow();
		}
		$row->name		= $ticketType['name'];
		$row->quantity	= $ticketType['quantity'];
		$row->event_id	= $ticketType['event_id'];
		$row->price		= $ticketType['price'];
		$row->details	= $ticketType['details'];
		$row->order		= $ticketType['order'];

		$row->save();
		return $row;
	}

	/**
	 * Return all ticket types (in order) with specific ticket-type-id.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Admin_Model_DbTable_Row_TicketType
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
		return $this->_ticketTypeTable->fetchAll($select);
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
		$this->_ticketTypeTable->delete($this->_ticketTypeTable->getAdapter()->quoteInto('ticket_type_id = ?', $ticketTypeId));
	}

	/**
	 * Save ticket.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	Attend_Db_Table_Row_Ticket
	 */
	public function saveTicket($ticket)
	{
		$this->getTicketTable();

		if(isset($ticket['ticket_id']))
		{
			$row = $this->_ticketTable->fetchRow($this->_ticketTable->select()
			->where(Attend_Db_Table_Tickets::getColumnName('ticketId'). ' = ?', $ticket['ticket_id']));
		}
		else
		{
			$row = $this->_ticketTable->createRow();
		}

		$row->setColumn('name', $ticket['name'])
			->setColumn('eventId', $ticket['event_id'])
			->setColumn('email', $ticket['email'])
			->setColumn('liuId', $ticket['liuid'])
			->setColumn('ticketTypeId', $ticket['ticket_type_id'])
			->setColumn('payment', $ticket['payment']);

		$row->save();
		return $row;
	}
}
