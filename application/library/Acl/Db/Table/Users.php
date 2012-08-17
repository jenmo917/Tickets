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

	/**
	 * Find a user with given LiU ID connected.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $userId
	 * @return	bool|array
	 */
	function findUser( $userId )
	{
		if ( is_string($userId) && strcmp('', $userId) )
		{
			$UserColName = $this->getColumnName('userId');
			$select = $this->select()->where($this->quoteString($UserColName). ' LIKE ?', $userId);
			$userLogin = $this->fetchRow($select);
			return ( $userLogin instanceof $this->_rowClass)? $userLogin->toArray(): false;
		}
		else
		{
			return false;
		}
	}
}
