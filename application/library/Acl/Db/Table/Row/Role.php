<?php
/**
 * Acl_Db_Table_Row_Role
 * @author		Daniel Josefsson
 * @version	0.1
 * @date		2012-05-18
 */
class Acl_Db_Table_Row_Role extends Generic_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Acl_Db_Table_Roles';
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
	* Primary column is role_id.
	*/
	protected $_primary	= 'role_id';
	/**#@-*/

	/**
	* Table columns
	* @access	protected
	* @var		array
	*/
	protected static $_columns =
		array(	'roleId'			=> 'role_id',
				'roleName'			=> 'role_name',
				'created'			=> 'created',
				'updated'			=> 'updated',);
}
