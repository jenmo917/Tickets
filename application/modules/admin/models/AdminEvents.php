<?php
class Admin_Model_AdminEvents
{
    /**
    * Instance of Admin_Model_DbTable_Events
    * @var Admin_Model_DbTable_Events $_eventsTable
    */
    protected $_eventsTable;
    
    /**
    * Instance of Admin_Model_DbTable_TicketTypes
    * @var Admin_Model_DbTable_Events $_ticketTypesTable
    */    
    protected $_ticketTypeTable;

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
    * Set Tickettypes table.
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Admin_Model_DbTable_TicketTypes
    */    
    public function setTicketTypesTable($dbTable)
    {
        if (is_string($dbTable))
        {
            $dbTable = new $dbTable();
            }
            if (!$dbTable instanceof Admin_Model_DbTable_TicketTypes)
            {
            throw new Exception('Invalid table data gateway provided');
                    }
                    $this->_ticketTypeTable = $dbTable;
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
    
    /**
    * Set or get ticket type table.
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Admin_Model_DbTable_TicketTypes
    */
    public function getTicketTypesTable()
    {
            if (null === $this->_ticketTypeTable)
            {
                    $this->setTicketTypesTable('Admin_Model_DbTable_TicketTypes');
            }
            return $this->_ticketTypeTable;
    }
    
    /*
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
        $row->name       = $event['name'];
        $row->location   = $event['location'];
        $row->details    = $event['details'];
        $row->public     = $event['public'];
        $row->start_time = $event['start_time'];
        $row->end_time   = $event['end_time'];        
        
        $row->save();
        return $row;
    }
        
    /*
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
    
    /*
    * Publish or unpublish event
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Admin_Model_DbTable_Row_Event
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
    
    /*
    * Fetch all events.
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Array with Admin_Model_DbTable_Row_Event
    */
    public function fetchEvents()
    {
        $this->getEventsTable();
        return $this->_eventsTable->fetchAll();
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

    /*
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
        $row->name       = $ticketType['name'];
        $row->quantity   = $ticketType['quantity'];
        $row->event_id   = $ticketType['event_id'];        
        $row->price      = $ticketType['price'];
        $row->details    = $ticketType['details'];
        $row->order      = $ticketType['order'];

        $row->save();
        return $row; 
    }    
    
    /*
    * Return all ticket types (in order) with specific ticket-type-id.
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Admin_Model_DbTable_Row_TicketType
    */     
    public function getTicketTypes($eventId)
    {
        $this->getTicketTypesTable();
        return $this->_ticketTypeTable->fetchAll($this->_ticketTypeTable->select()->where('event_id = ?', $eventId)->order('order'));
    }
    
    /*
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
    
}