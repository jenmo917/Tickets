<?php
/**
 * Acl_Db_Table_Users
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_Users extends Acl_Db_Table_Abstract
{
	/**
	* Table name constant.
	* @var const
	*/
	const TABLE_NAME 	= 'users';
	const ROW_CLASS		= 'Acl_Db_Table_Row_User';

	/**#@+
	* @access	private
	* @var		string
	*/
	/**
	* The table name is acl_roles.
	*/
	protected $_name		= self::TABLE_NAME;

	/**
	* The row class is given by ROW_CLASS
	*/
	protected $_rowClass	= self::ROW_CLASS;
	/**#@-*/

	/**
	 * Dependent tables is just Acl_Db_Table_UserLiuLogins.
	 * @access private
	 * @var		array
	 */
	protected $_dependentTables	= array('Acl_Db_Table_UserLiuLogins',);
}
