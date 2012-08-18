<?php
class Acl_Acl extends Zend_Acl
{
	protected $_userInfoSession;
	public function isAllowed( $role = null, $resource = null, $privilege = null)
	{
		//$pre = Acl_Resources::PAGEPREFIX;
		$del = Acl_Resources::DELIMITER;
		// Fix $roles so it includes $role or current user roles.
		if ( null === $role )
		{
			$this->_loadUserInfoSession();
			if ( !($this->_userInfoSession instanceof Login_Model_UserInfoSession) )
				throw new Zend_Exception('Failed to load Login_Model_UserInfoSession');
			$roles = $this->_userInfoSession->getActiveRoleIds();
		}
		elseif (is_string($role) || $role instanceof Zend_Acl_Role_Interface)
			$roles = array($role);
		else
			throw new Zend_Acl_Exception('$role must be a string or an instance of Zend_Acl_Role_Interface.');

		// Fix $resources so it includes $role or current user roles.
		if (!is_string($resource) && !($resource instanceof Zend_Acl_Resource_Interface))
			throw new Zend_Acl_Exception('$resource must be a string or an instance of Zend_Acl_Resource_Interface.');

		if ( !strcmp('resourceStackCheck', $privilege) )
		{
			list($prefix, $module, $controller, $action) = explode($del, $resource);
			$resourceNeedels
			= array	(
				'all'			=> $prefix.$del.'*'		.$del.'*'			.$del.'*',
				'module'		=> $prefix.$del.$module	.$del.'*'			.$del.'*',
				'controller'	=> $prefix.$del.$module	.$del.$controller	.$del.'*',
				'action'		=> $prefix.$del.$module	.$del.$controller	.$del.$action
			);
		}
		else
			$resourceNeedels = array('single' => $resource);


		// Initiate the access result array.
		$levels = array_keys($resourceNeedels);
		$access = array	();
		foreach ( $levels as $level)
		{
			$access[$level] = null;
		}

		foreach ($resourceNeedels as $level => $resource)
		{
			if ( $this->has($resource) )
			{
				foreach ($roles as $role)
				{
					if ( $this->hasRole($role) )
					{
						// Check if the role has deny set for this resource.
						if(parent::isAllowed($role, $resource, 'deny'))
							$access[$level] = false;
						// Check if the role has allow set for this resource.
						if(parent::isAllowed($role, $resource, 'allow'))
							$access[$level] = true;
						// Check if the role has deny set for this resource.
						if($access[$level])
							break;
					}
				}
			}
		}

		// Sweep trough $access to decide if the user is permitted to access this resource.
		$result = false;
		foreach ($access as $level => $permission)
		{
			if ( isset($access[$level]) && is_bool($permission) )
			{
				$result = $permission;
			}
		}
		return $result;
	}

	private function _loadUserInfoSession()
	{
		if ( !$this->_userInfoSession instanceof Login_Model_UserInfoSession )
		{
			$this->_userInfoSession = new Login_Model_UserInfoSession();
		}
	}
}