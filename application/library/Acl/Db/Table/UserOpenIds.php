<?php
/**
 * Acl_Db_Table_UserOpenIds
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_UserOpenIds extends Generic_Db_Table_Abstract implements Acl_LoginService_ReferenceInterface
{
	/**
	* Table name constant.
	* @var const
	*/
	const TABLE_NAME	= 'acl_user_open_ids';
	const ROW_CLASS		= 'Acl_Db_Table_Row_UserOpenId';

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
	 * Internal function to find a user with given openId connected.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @access	protected
	 * @param	string $identifier
	 * @param	book $toArray set to false if you want to return an object if found.
	 * @return	bool|array
	 */
	protected function _findByIdentifier($identifier, $toArray = true)
	{
		if ( is_string($identifier) && strcmp('', $identifier) )
		{
			$identifierColName = $this->getColumnName('openId');
			$usersTableName = Acl_Db_Table_Users::getTableName();

			$select = $this->select()->setIntegrityCheck(false)->from($this->info('name'))
			->where($this->quoteString($this->info('name')).'.'.$this->quoteString($identifierColName). ' LIKE ?', $identifier)
			->joinLeft(	$usersTableName,
						$this->quoteString($this->info('name')) . '.' . $this->quoteString($this->getColumnName('userId')) . ' = ' .
						$this->quoteString($usersTableName) . '.' . $this->quoteString(Acl_Db_Table_Users::getColumnName('userId')),
						array());//
			$userLogin = $this->fetchRow($select);
			// TODO: Get parent row search to work.
			//$userRow = $userLogin->findParentRow('Acl_Db_Table_Users');//, 'User'
			if ( $userLogin )
			{
				return ($toArray)?$userLogin->toArray():$userLogin;//$userRow;;
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
	
	/**
	 * Find a user with given openId connected.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @access	protected
	 * @param	string $identifier
	 * @return	bool|array
	 */
	public function findByIdentifier($identifier)
	{
		return $this->_findByIdentifier($identifier);
	}
	
	public function storeAccount($userId, $identifier)
	{
		$alreadyUser = $this->_findByIdentifier($identifier);
		$rowClass = self::ROW_CLASS;
		if (!$alreadyUser instanceof $rowClass)
		{
			$this	->createRow()
					->setColumn('userId', $userId)
					->setColumn('openId', $identifier)
					->save();
		}
		else 
		{
			$alreadyUser->setColumn('updated', date( 'Y-m-d H:i:s'))
						->save();
		}
	}
}
