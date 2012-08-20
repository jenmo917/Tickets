<?php
/**
 * Acl_Db_Table_DefaultPrivileges
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_DefaultPrivileges extends Generic_Db_Table_Abstract
{
	/**
	* Table name constant.
	* @var const
	*/
	const TABLE_NAME	= 'acl_default_privileges';
	const ROW_CLASS		= 'Acl_Db_Table_Row_DefaultPrivilege';

	/**
	 * #@+
	 * @access	private
	 * @var		string
	 */

	/**
	 * The table name is given by TABLE_NAME.
	 */
	protected $_name		= self::TABLE_NAME;

	/**
	* The row class is given by ROW_CLASS
	*/
	protected $_rowClass	= self::ROW_CLASS;
	/**
	 * #@-
	 */

	public function getCategoriesPrivileges($categories)
	{
		if(!is_array($categories))
		{
			throw new Zend_Exeption('Input has to be array');
		}

		$select = $this->select();
		$categoryColName = $this->getColumnName('category');
		foreach ($categories as $category)
		{
			$select->orWhere($categoryColName. ' LIKE ?', $category);
		}
		return $this->fetchAll($select)->toArray();
	}
}
