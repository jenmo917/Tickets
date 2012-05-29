<?php

class Admin_Model_DbTable_TicketTypes extends Zend_Db_Table_Abstract
{
    protected $_name        = 'ticket_types';
    protected $_rowClass    = 'Admin_Model_DbTable_Row_TicketType';
}