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

	protected $_availableOptions = array
		(	'privilegeId',
			'roleId',
			'organisationId',
			'eventId',
			'active'			);

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

	public function getEventId()
	{
		return $this->_eventId;
	}

	public function isActive()
	{
		$now = date('Y-m-d H:i:s');
		return (	( null === $this->_startTime 	|| (is_string($this->_startTime)	&& !strcmp($this->_startTime, 	'0000-00-00 00:00:00')) || $this->_startTime 	< $now) &&
					( null === $this->_endTime		|| (is_string($this->_endTime)		&& !strcmp($this->_endTime,		'0000-00-00 00:00:00')) || $this->_endTime		< $now) )?
			true: false;
	}

	public function followsPattern($settings)
	{
		if ( !is_array($settings) )
		{
			throw new Zend_Acl_Exception('$settings must be an array');
		}
		$givenOptions = array_keys($settings);
		$diff = array_diff($givenOptions, $this->_availableOptions);
		if (!empty($diff))
		{
			$diffStr = implode(', ', $diff);
			throw new Zend_Acl_Exception('Following keys are not valid options: ', $diffStr);
		}
		$result = true;
		foreach ($settings as $option => $value)
		{
			switch ($option)
			{
				case 'privilegeId':
					if ( $value !== $this->_privilegeId )
						$result = false;
					break;
				case 'roleId':
					if ( $value !== $this->_roleId )
						$result = false;
					break;
				case 'organisationId':
					if ( $value !== $this->_organisationId )
						$result = false;
					break;
				case 'eventId':
					if ( $value !== $this->_eventId )
						$result = false;
					break;
				case 'active':
					if ( !$this->isActive() )
						$result = false;
					break;
				default:
					$result = false;
					break;
			}
			if (false === $result)
				break;
		}
		return $result;
	}
}
