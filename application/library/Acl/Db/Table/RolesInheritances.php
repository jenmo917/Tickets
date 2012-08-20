<?php
/**
 * Acl_Db_Table_Roles
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_RolesInheritances extends Generic_Db_Table_Abstract
{
		/**
	* Table name constant.
	* @var const
	*/
	const TABLE_NAME 	= 'acl_roles_inheritances';
	const ROW_CLASS		= 'Acl_Db_Table_Row_RolesInheritance';

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
}
