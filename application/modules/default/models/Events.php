<?php
class Default_Model_Events
{
    /**
    * Instance of Admin_Model_DbTable_Events
    * @var Admin_Model_DbTable_Events $_eventsTable
    */
    protected $_eventsTable;

    /**
    * Set Events table.
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Admin_Model_DbTable_Events
    */        
    public function setEventsTable($dbTable)
    {
        if (is_string($dbTable))
        {
            $dbTable = new $dbTable();
            }
            if (!$dbTable instanceof Admin_Model_DbTable_Events)
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
    * @return	Admin_Model_DbTable_Events
    */
    public function getEventsTable()
    {
            if (null === $this->_eventsTable)
            {
                    $this->setEventsTable('Admin_Model_DbTable_Events');
            }
            return $this->_eventsTable;
    }
    
    /*
    * Fetch published events.
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Array with Admin_Model_DbTable_Row_Event
    */
    public function fetchPublishedEvents()
    {
        $this->getEventsTable();
        return $this->_eventsTable->fetchAll($this->_eventsTable->select()->where('published = ?', 1));
    }
    
    
    /*
    * Return one event with specific event-id.
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Admin_Model_DbTable_Row_Event
    */     
    public function getEvent($eventId)
    {
        $this->getEventsTable();
        return $row = $this->_eventsTable->fetchRow($this->_eventsTable->select()->where('event_id = ?', $eventId));
    }
}