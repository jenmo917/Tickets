<?php
class Acl_Privileges
{
	/**
	 * User id.
	 * @var string|int
	 */
	protected $_userId = null;

	/**
	 * Privileges
	 * @var array
	 */
	protected $_privileges = array();

	/**
	 * Sets the user id.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param string|int $userId
	 */
	public function __construct($userId = null)
	{
		$this->setUserId($userId);
	}

	/** Add Acl_Privilege.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param Acl_Privilege $privilege
	 */
	public function addPrivilege(Acl_Privilege $privilege)
	{
		return $this->_privileges[$privilege->getPrivilegeId()] = $privilege;
	}

	/**
	 * Find a privilege with a given id. Returns false if not found.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $id
	 * @return	Acl_Privilege|boolean
	 */
	public function findPrivilege($id, $onlyActive = false)
	{
		foreach ($this->_privileges as $privilege)
		{
			if ( $id === $privilege->getRoleId() )
			{
				if ( $onlyActive )
				{
					return ( $privilege->isActive() )? $privilege: false;
				}
				else
				{
					return $privilege;
				}
			}
		}
		return false;
	}

	/**
	 * Check if a privilege is stored inside.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $id
	 * @return	boolean
	 */
	public function hasPrivilege($id, $onlyActive = false)
	{
		foreach ($this->_privileges as $privilege)
		{
			if ( $id === $privilege->getPrivilegeId() )
			{
				if ( $onlyActive )
				{
					return ( $privilege->isActive() )? true: false;
				}
				else
				{
					return $privilege;
				}
			}
		}
		return false;
	}

	/**
	 * Return all stored privileges.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	array
	 */
	public function getPrivileges($onlyActive = false)
	{
		if ( $onlyActive )
		{
			$activePrivileges = array();
			foreach ($this->_privileges as $privilege)
			{
				( $privilege->isActive() )? $activePrivileges[$privilege->getRoleId()] = $privilege: null;
			}
		}
		else
		{
			return $this->_privileges;
		}
	}

	/**
	 * Clear all privileges
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	Acl_Privileges
	 */
	public function clear()
	{
		unset($this->_privileges);
		$this->_privileges = array();
		$this->_userId = null;
		return $this;
	}

	/**
	 * Sets user id.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string|int $userId
	 * @throws	Zend_Exception
	 */
	public function setUserId($userId = null)
	{
		if ( !(is_string($userId) || is_int($userId) || is_null($userId)) )
		{
			throw new Zend_Exception('Given user id is not of string or int.');
		}
		$this->_userId = $userId;
		return $this;
	}

	/**
	 * Returns user id.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	string|int
	 */
	public function getUserId()
	{
		return $this->_userId;
	}

	/**
	 * Get all role ids.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	array
	 */
	public function getRoleIds()
	{
		$roleIds = array();
		foreach ($this->_privileges as $privilege)
		{
			$roleIds[] = $privilege->getRoleId();
		}
		return $roleIds;
	}

	/**
	 * Remove privileges that no longer exists or that has expired by give a list of which keys(privilege ids) that should exist.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param 	array		$keyArrRoleIds	Array with privileges that should exist.
	 * @return	Acl_Privileges
	 */
	public function sanitize($keyArrRoleIds)
	{
		$excessKeys = array_diff(array_keys($this->_privileges), $keyArrRoleIds);
		foreach ($excessKeys as $excessKey)
		{
			unset($this->_privileges[$excessKey]);
		}
		return $this;
	}
}
