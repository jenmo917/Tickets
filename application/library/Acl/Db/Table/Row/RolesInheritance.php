<?php
/**
 * Login_Model_Db_Table_Row_Role
 * @author		Daniel Josefsson
 * @version	0.1
 * @date		2012-05-18
 */
class Acl_Db_Table_Row_RolesInheritance extends Generic_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Acl_Db_Table_RolesInheritances';
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
	* Primary columns are role_id and parent_role_id.
	* @var	array
	*/
	protected $_primary	= array(	'role_id',
										'parent_role_id');
	/**#@-*/

	/**
	 * Table columns
	 * @access	protected
	 * @var		array
	 */
	protected static $_columns =
		array(	'roleId'			=> 'role_id',
				'parentRoleId'		=> 'parent_role_id',
				'created'			=> 'created',
				'updated'			=> 'updated',);
}
