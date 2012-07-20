<?php

class Admin_Model_DbTable_Row_Ticket extends Zend_Db_Table_Row_Abstract
{
    protected $_tableClass      = 'Admin_Model_DbTable_Tickets';
    protected $_primary         = 'ticket_id';
    protected $_name            = 'name';    
    protected $_email           = 'email';
    protected $_liuid           = 'liuid';
    protected $_ticket_type_id  = 'ticket_type_id';
    protected $_payment         = 'payment';
}