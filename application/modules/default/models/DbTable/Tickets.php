<?php

class Admin_Model_DbTable_Tickets extends Zend_Db_Table_Abstract
{
    protected $_name        = 'tickets';
    protected $_rowClass    = 'Admin_Model_DbTable_Row_Ticket';
}