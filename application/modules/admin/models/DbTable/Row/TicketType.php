<?php

class Admin_Model_DbTable_Row_TicketType extends Zend_Db_Table_Row_Abstract
{
    protected $_tableClass    = 'Admin_Model_DbTable_TicketTypes';
    protected $_primary       = 'ticket_type_id';
    protected $_eventId       = 'event_id';    
    protected $_name          = 'name';
    protected $_price         = 'price';
    protected $_quantity      = 'quantity';
    protected $_details       = 'details';
}