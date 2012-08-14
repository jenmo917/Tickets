<?php

class Admin_Model_DbTable_Row_Event extends Generic_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Admin_Model_DbTable_Events';
	/**
	 * #@+
	 * @access	protected
	 */

	/**
	 * The table class is given by TABLE_CLASS.
	 * @var		string
	 */
	protected $_tableClass 	= self::TABLE_CLASS;

	/**
	 * Primary column is permission_id
	 * @var		string
	 */
	protected $_primary		= 'event_id';
	/**#@-*/

	protected static $_columns
		= array(	'eventId'		=> 'event_id',
					'name'			=> 'name',
					'details'		=> 'details',
					'location'		=> 'location',
					'public'		=> 'public',
					'published'		=> 'published',
					'startTime'		=> 'start_time',
					'endTime'		=> 'end_time',
					'created'		=> 'created',
					'updated'		=> 'updated');
}
