<?php

class Admin_Model_DbTable_Row_TicketType extends Generic_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Admin_Db_Table_TicketTypes';
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
	protected $_primary		= 'ticket_type_id';
	/**#@-*/

	protected static $_columns
		= array(	'ticketTypeId'	=> 'ticket_type_id',
					'eventId'		=> 'event_id',
					'name'			=> 'name',
					'price'			=> 'price',
					'quantity'		=> 'quantity',
					'details'		=> 'details',
					'order'			=> 'order',
					'created'		=> 'created',
					'updated'		=> 'updated');
}
