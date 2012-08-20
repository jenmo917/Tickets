<?php
/**
 * Acl_Db_Table_Permissions
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_Permissions extends Generic_Db_Table_Abstract
{
	/**
	* Table name constant.
	* @var const
	*/
	const TABLE_NAME 	= 'acl_permissions';
	const ROW_CLASS		= 'Acl_Db_Table_Row_Permission';

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

	public function getPermissionsArrayWithResourceNames()
	{
		$resourcesTableName = Acl_Db_Table_Resources::getTableName();
		$select = $this->select()->setIntegrityCheck(false)->from($this->_name)
		->joinLeft(	$resourcesTableName,
					$this->quoteString($this->_name) . '.' . $this->quoteString($this->getColumnName('resourceId')) . ' = ' .
					$this->quoteString($resourcesTableName) . '.' . $this->quoteString(Acl_Db_Table_Resources::getColumnName('resourceId')),
						array(Acl_Db_Table_Resources::getColumnName('resource')));
		return $this->fetchAll($select)->toArray();
	}
}
