<?php

class Admin_Model_DbTable_Events extends Zend_Db_Table_Abstract
{
    protected $_name        = 'events';
    protected $_rowClass    = 'Admin_Model_DbTable_Row_Event';
}