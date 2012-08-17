<?php
/**
 * Acl_Db_Table_Row_DefaultPrivilege
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_Row_DefaultPrivilege extends Acl_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Acl_Db_Table_DefaultPrivileges';
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
	* Primary column is deafult_privilege_id.
	*/
	protected $_primary	= 'deafult_privilege_id';
	/**#@-*/

	/**
	* Table columns
	* @access	protected
	* @var		array
	*/
	protected static $_columns =
		array(	'defaultPrivilegeId'	=> 'deafult_privilege_id',
				'roleId'				=> 'role_id',
				'category'				=> 'category',
				'created'				=> 'created',
				'updated'			=> 'updated',);
}
