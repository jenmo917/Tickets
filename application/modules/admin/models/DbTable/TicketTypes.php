<?php

class Admin_Model_DbTable_TicketTypes extends Generic_Db_Table_Abstract
{
	/**
	 * Table name constant.
	 * @var const
	 */
	const TABLE_NAME 	= 'ticket_types';

	/**
	 * Row class constant.
	 * @var const
	 */
	const ROW_CLASS		= 'Admin_Model_DbTable_Row_TicketType';

	/**#@+
	 * @access	private
	 * @var		string
	 */
	/**
	 * The table name is acl_roles.
	 */
	protected $_name		= self::TABLE_NAME;

	/**
	 * The row class is given by ROW_CLASS.
	 */
	protected $_rowClass	= self::ROW_CLASS;
	/**#@-*/
}
