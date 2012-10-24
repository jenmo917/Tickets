<?php
class Default_Model_Events
{
    /**
    * Instance of Attend_Db_Table_Events
    * @var Attend_Db_Table_Events $_eventsTable
    */
    protected $_eventsTable;

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

    /*
    * Fetch published events.
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Array with Attend_Db_Table_Row_Event
    */
    public function fetchFrontPageEvents()
    {
        $this->getEventsTable();
        return $this->_eventsTable->fetchAll($this->_eventsTable->select()->where('published = ?', 1)->where('public = ?', 1));
    }


    /*
    * Return one event with specific event-id.
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Attend_Db_Table_Row_Event
    */
    public function getEvent($eventId)
    {
        $this->getEventsTable();
        return $row = $this->_eventsTable->fetchRow($this->_eventsTable->select()->where('event_id = ?', $eventId));
    }

	public function getEventByName($eventName)
	{
		$this->getEventsTable();
		$select = $this->_eventsTable->select()->where(Attend_Db_Table_Row_Event::getColumnName('name').' LIKE ?', $eventName)->limit(1);
		return $row = $this->_eventsTable->fetchRow($select);
	}
}