<?php
/**
 * Acl_Db_Table_Row_Permission
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_Row_Permission extends Acl_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Acl_Db_Table_Permissions.';
	/**
	 * #@+
	 * @access	protected
	 *
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
	protected $_primary		= array('role_id', 'resource_id');
	/**#@-*/

	protected static $_columns
		= array(	'roleId'		=> 'role_id',
					'resourceId'	=> 'resource_id',
					'permission'	=> 'permission',
					'assertion'		=> 'assertion',
					'created'		=> 'created',
					'updated'		=> 'updated');
}
