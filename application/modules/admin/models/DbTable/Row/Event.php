<?php

class Admin_Model_DbTable_Row_Event extends Zend_Db_Table_Row_Abstract
{
    protected $_tableClass    = 'Admin_Model_DbTable_Events';
    protected $_primary       = 'event_id';
    protected $_name          = 'name';
    protected $_details       = 'details';
    protected $_location      = 'location';
    protected $_public        = 'public';
    protected $_published     = 'published';
    protected $_startTime     = 'start_time';
    protected $_endTime       = 'end_time';
    protected $_created       = 'created';
}