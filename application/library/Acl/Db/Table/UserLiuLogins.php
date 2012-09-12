<?php
/**
 * Acl_Db_Table_UserLiuLogins
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_UserLiuLogins extends Generic_Db_Table_Abstract
{
	/**
	* Table name constant.
	* @var const
	*/
	const TABLE_NAME 	= 'acl_user_liu_logins';
	const ROW_CLASS		= 'Acl_Db_Table_Row_UserLiuLogin';

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
	 * Reference to Acl_Db_Table_Users
	 * @access	private
	 * @var		array
	 */
	protected $_referenceMap	= array
		(
			'User' => array
				(
				'columns'		=> array('user_id',),
				'refTableClass'	=> array('Acl_Db_Table_Users',),
				'refColumns'	=>	array('user_id'),
				),
		);

	/**
	 * Find a user with given LiU ID connected.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $liuId
	 * @return	bool|array
	 */
	function findLiuId( $liuId )
	{
		if ( is_string($liuId) && strcmp('', $liuId) )
		{
			$liuIdColName = $this->getColumnName('liuId');
			$usersTableName = Acl_Db_Table_Users::getTableName();

			$select = $this->select()->setIntegrityCheck(false)->from($this->info('name'))
			->where($this->quoteString($this->info('name')).'.'.$this->quoteString($liuIdColName). ' LIKE ?', $liuId)
			->joinLeft(	$usersTableName,
						$this->quoteString($this->info('name')) . '.' . $this->quoteString($this->getColumnName('userId')) . ' = ' .
						$this->quoteString($usersTableName) . '.' . $this->quoteString(Acl_Db_Table_Users::getColumnName('userId')),
						array());//
			$userLogin = $this->fetchRow($select);
			// TODO: Get parent row search to work.
			//$userRow = $userLogin->findParentRow('Acl_Db_Table_Users');//, 'User'
			if ( $userLogin )
			{
				return $userLogin->toArray();//$userRow;;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}
