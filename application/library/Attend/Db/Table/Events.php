<?php

class Attend_Db_Table_Events extends Generic_Db_Table_Abstract
{
	/**
	 * Table name constant.
	 * @var const
	 */
	const TABLE_NAME 	= 'events';

	/**
	 * Row class constant.
	 * @var const
	 */
	const ROW_CLASS		= 'Attend_Db_Table_Row_Event';

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
