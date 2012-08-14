<?php

class Admin_Model_DbTable_Row_Ticket extends Generic_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Admin_Db_Table_Tickets';
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
	 * @var		array
	 */
	protected $_primary		= 'ticket_id';
	/**#@-*/

	protected static $_columns
		= array(	'ticketId'		=> 'ticket_id',
					'eventId'		=> 'event_id',
					'user_id'		=> 'user_id',
					'name'			=> 'name',
					'email'			=> 'email',
					'liuId'			=> 'liu_id',
					'ticketTypeId'	=> 'ticket_type_id',
					'payment'		=> 'payment',
					'created'		=> 'created',
					'updated'		=> 'updated');
}
