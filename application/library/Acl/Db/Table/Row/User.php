<?php
/**
 * Acl_Db_Table_Row_User
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_Row_User extends Generic_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Acl_Db_Table_Users';
	/**
	 * #@+
	 * @access	protected
	 * @var		string
	 */

	/**
	 * The table class is given by TABLE_CLASS.
	 */
	protected $_tableClass 	= self::TABLE_CLASS;

	/**
	 * Primary column is user_id.
	 */
	protected $_primary	= 'user_id';

	/**
	 * Table columns
	 * @access	protected
	 * @var		array
	 */
	protected static $_columns =
		array(	'userId'			=> 'user_id',//array('columnName' => 'user_id', 'primary' => 'true'),
				'created'			=> 'created',
				'updated'			=> 'updated',);
}
