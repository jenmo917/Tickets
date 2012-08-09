<?php
class Acl_Privilege
{
	protected $_privilegeId	= null;
	protected $_userId			= null;
	protected $_organisationId	= null;
	protected $_eventId		= null;
	protected $_roleId			= null;
	protected $_startTime		= null;
	protected $_endTime		= null;
	protected $_created		= null;
	protected $_updated		= null;

	/**
	 * Parameterizes Acl_Db_Table_Row_Privilege and extends it so it can
	 * store both parent and child inheritances.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param Acl_Db_Table_Row_Privilege $privilege
	 */
	public function __construct(Acl_Db_Table_Row_Privilege $privilege)
	{
		$arrPrivilege = $privilege->toArray();
		$this->_privilegeId		= $privilege->getColumn('privilegeId');
		$this->_userId			= $privilege->getColumn('userId');
		$this->_organisationId	= $privilege->getColumn('organisationId');
		$this->_eventId			= $privilege->getColumn('eventId');
		$this->_roleId			= $privilege->getColumn('roleId');
		$this->_startTime		= $privilege->getColumn('startTime');
		$this->_endTime			= $privilege->getColumn('endTime');
		$this->_created			= $privilege->getColumn('created');
		$this->_updated			= $privilege->getColumn('updated');
		return $this;
	}

	/**
	 * Get privilege ID.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return string
	 */
	public function getPrivilegeId()
	{
		return $this->_privilegeId;
	}

	/**
	 * Get role ID.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return string
	 */
	public function getRoleId()
	{
		return $this->_roleId;
	}

	public function isActive()
	{
		$now = date('Y-m-d H:i:s');
		return ( $this->_startTime < $now && ( $this->_endTime > $now || !strcmp($this->_endTime, '0000-00-00 00:00:00' ) ) )?
			true: false;
	}
}
