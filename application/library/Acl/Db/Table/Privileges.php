<?php
/**
 * Acl_Db_Table_Privileges
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_Privileges extends Generic_Db_Table_Abstract
{
	/**
	 * Table name constant.
	 * @var const
	 */
	const TABLE_NAME = 'acl_privileges';
	const ROW_CLASS	  = 'Acl_Db_Table_Row_Privilege';
	/**#@+
	* @access	private
	* @var		string
	*/

	/**
	 * The table name is acl_privileges.
	 */
	protected $_name = self::TABLE_NAME;

	/**
	 * The row class is given by ROW_CLASS
	 */
	protected $_rowClass	= self::ROW_CLASS;
	/**#@-*/

	public function getPrivilegesForUserId( $userId = null, $onlyActive = false )
	{
		$where  =	$this->quoteString($this->getColumnName('userId'));
		$where .=	is_null($userId)?
					' IS NULL':
					' = ?';
		$select = $this->select()->setIntegrityCheck(false)->from($this->_name)->where($where, $userId);
		if ( $onlyActive )
		{
			$now = date('Y-m-d H:i:s');
			$select	->where($this->quoteString($this->getColumnName('startTime'))	." < '". $now."' OR ".
							$this->quoteString($this->getColumnName('startTime'))	." IS NULL")
					->where($this->quoteString($this->getColumnName('endTime'))		." > '". $now."' OR ".
							$this->quoteString($this->getColumnName('endTime'))		." = '0000-00-00 00:00:00' OR ".
							$this->quoteString($this->getColumnName('endTime'))		." IS NULL");
		}
		return $this->fetchAll($select);
	}
}
