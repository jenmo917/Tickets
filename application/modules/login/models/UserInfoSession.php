<?php
// TODO: Auto logout a user after AUTO_LOGOUT time.
// TODO: Make it possible to connect accounts.
class Login_Model_UserInfoSession
{
	static protected $_info = array('user', 'privileges');
	protected $_namespace;
	protected $_services;
	protected $_users;
	protected $_defaultPrivileges;
	static protected $_defaultPrivilegeCategories = array('authenticated user');
	const USER_INFO_NS = 'Attend_UserInfo';
	const AUTO_LOGOUT	= 900; // 15 minutes.

	/**
	 * Sets the namespace and fetches user privileges.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @access	public
	 */
	public function __construct()
	{
		// Try to fetch an old instance of this namespace USER_INFO_NS.
		if ( Zend_Registry::isRegistered(self::USER_INFO_NS) )
		{
			$this->_namespace = Zend_Registry::get(self::USER_INFO_NS);
		}
		else
		{
			$this->_namespace
				= new Zend_Session_Namespace(self::USER_INFO_NS, true);
			Zend_Registry::set(self::USER_INFO_NS, $this->_namespace);
		}

		$this->_setupServices();
		// Make sure that user key exists.
		if ( !isset($this->_namespace->user) )
		{
			$this->_namespace->user = null;
		}

		//Get all privileges for this user.
		$this->_getPrivileges();
	}

	/**
	 * #@+
	 * @access	protected
	 */

	/**
	 * Set user id, create timestamp and update timestamp to session.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param int $userId
	 */
	protected function _setUserInformation( $userId )
	{
		$users = new Acl_Db_Table_Users();
		// Get an array over the user data for the given user id.
		$userData = $users->find($userId)->current()->toArray();

		// Set variables.
		if ( !is_array($this->_namespace->user) )
		{
			$this->_namespace->user = array();
		}

		// Check if a user already is in, if so; make sure that right privileges is set.
		if (	isset($this->_namespace->user['userId']) &&
				$this->_namespace->user['userId'] !== (int) $userId )
		{
			if (	isset($this->_namespace->privileges) &&
					is_array($this->_namespace->privileges) )
			{
				$this->_namespace->__unset('privileges');
				$this->_namespace->privileges = array();
			}
		}
		$this->_namespace->user['userId']	= $userId;
		$this->_namespace->user['created']	=
			$userData[$users->getColumnName('created')];
		$this->_namespace->user['updated']	=
				$userData[$users->getColumnName('updated')];
	}

	/**
	 * Get privileges for current user. Privileges is set to USER_INFO_NS->privileges.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @uses	Acl_Privileges
	 */
	protected function _getPrivileges()
	{
		// Fetch user id and use null if no one is logged in.
		$userId = $this->getUserId();

		// Setup the privileges holder.
		if ( !($this->_namespace->privileges instanceof Acl_Privileges) )
		{
			$this->_namespace->privileges = new Acl_Privileges($userId);
		}
		// A check so the privileges is recorded for the right users.
		elseif ($this->_namespace->privileges->getUserId() !== $userId)
		{
			$this->_namespace->privileges->clear()->setUserId($userId);
		}

		$userPrivileges = Acl_Factory::getPrivilegesForUser($userId);
		$validPermissionIds = array();
		foreach ($userPrivileges as $userPrivilege)
		{
			$this->_namespace->privileges->addPrivilege(new Acl_Privilege($userPrivilege));
			$validPermissionIds[] = $userPrivilege->getColumn('privilegeId');
		}
		$this->_namespace->privileges->sanitize($validPermissionIds);
	}

	/**
	 * Check wether a service is registered or not.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $serviceName
	 * @return	bool
	 * @throws	Zend_Exception if the input is not a string.
	 */
	protected function _isService($serviceName)
	{
		if ( !is_string($serviceName) )
		{
			throw new Zend_Exception('Input must be a string');
		}
		$availableServices = $this->getServices();
		return ( is_string($serviceName) && in_array($serviceName, $availableServices) )? true:false;
	}

	/**
	 * Sets up services given in $availableServicesClassNames
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return Login_Model_UserInfoSession
	 */
	protected function _setupServices()
	{
		$availableServicesClassNames
			= array('Login_Model_LiuInfoSession');
		foreach ($availableServicesClassNames as $className)
		{
			$this->_services = array(
				$className::getServiceName() =>
					array(	'className'	=> $className,
							'object'	=> new $className()));
		}
	return $this;
	}

	/**
	 * Load the table where the users are stored.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	protected function _loadUsersTable()
	{
		if(!$this->_users instanceof Acl_Db_Table_Users)
		{
			$this->_users = new Acl_Db_Table_Users();
		}
	}

	/**
	 * Load the table where the default privileges are stored.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	protected function _loadDefaultPrivilegesTable()
	{
		if(!$this->_defaultPrivileges instanceof Acl_Db_Table_DefaultPrivileges)
		{
			$this->_defaultPrivileges = new Acl_Db_Table_DefaultPrivileges();
		}
	}

	/**
	 * Sets user info to namespace.
	 * Keys in $userInfo must conform with column names of Acl_Db_Table_Users
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array $userInfo
	 * @throws Zend_Exception
	 */
	protected function _setInfo( $userInfo )
	{
		// Make sure that all keys are in the input.
		$colNames = array(	'userId'	=> Acl_Db_Table_Users::getColumnName('userId'),
							'created'	=> Acl_Db_Table_Users::getColumnName('created'),
							'updated'	=> Acl_Db_Table_Users::getColumnName('updated'));
		if ( !is_array($userInfo) )
		{
			throw new Zend_Exception('Input has to be an array.');
		}
		$diff = array_diff($colNames, array_keys($userInfo));
		if ( !empty($diff) )
		{
			$keysString = '';
			foreach ($colNames as $colName)
			{
				$keysString .= $colName. ', ';
			}
			$keysString = substr($keysString, 0, -2);
			throw new Zend_Exception($keyString.' must be all be keys in input');
		}

		// Set up the user namespace array.
		if ( !isset($this->_namespace->user) || !is_Array($this->_namespace->user) )
		{
			$this->_namespace->user = array();
		}
		$this->_namespace->user =
		array(	'userId' => $userInfo[$colNames['userId']],
				'created' => $userInfo[$colNames['created']],
				'updated' => $userInfo[$colNames['updated']],);
	}

	/**
	 * Create a new user, set default privileges and store it.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	string	user id
	 */
	protected function _createNewUser()
	{
		$this->_loadUsersTable();
		$userId = $this->_users->createRow()->save();
		// Reload the user to get default values set by the storage mechanism.
		$userInfo = $this->_users->findUser($userId);
		$this->_setInfo($userInfo);
		$this->_addDefaultPrivileges($userId, self::$_defaultPrivilegeCategories);
		return $userId;
	}

	/**
	 * Add default privileges to user on given categories.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string	$userId
	 * @param	array	$categories
	 * @todo	Limit so the user cant get serveral of the same privileges.
	 * 			As for now, the user will get several equal service default privileges.
	 * @throws	Zend_Exception if $userId is not a string
	 * @throws	Zend_Exception if $categories is not an array.
	 */
	protected function _addDefaultPrivileges( $userId, $categories )
	{
		if ( !is_string($userId) )
			throw new Zend_Exception('$userId must be a string');
		if (!is_array($categories))
		throw new Zend_Exception('$categories must be an array');

		$roleIdPrivilegeColName = Acl_Db_Table_Privileges::getColumnName('roleId');
		$userIdPrivilegeColName = Acl_Db_Table_Privileges::getColumnName('userId');

		$this->_loadDefaultPrivilegesTable();
		$roleIdColName = $this->_defaultPrivileges->getColumnName('roleId');

		//Fetch all privileges that should be set to the user.
		$privilegesArray = $this->_defaultPrivileges->getCategoriesPrivileges($categories);
		foreach ($privilegesArray as $privilege)
		{
			$settings = array(	$roleIdPrivilegeColName => $privilege[$roleIdColName],
			$userIdPrivilegeColName => $userId);
			// Store the privilege.
			Acl_Factory::addPrivilegeToStorage($settings);
		}
	}

	/**
	 * Clear user info from namespace.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	bool
	 */
	protected function _userLogout()
	{
		$this->_namespace->privileges->clear();
		$this->_namespace->unsetAll();
		return true;
	}

	/**
	 * #@-
	 * #@+
	 * @access public
	 */

	/**
	 * Return this namespace as an array.
	 * If $authServices is left empty,
	 * the whole namespace will be returned as an array.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array|string	$authServices
	 * @return	array
	 * @throws	Zend_Exception if input is not string or array.
	 */
	public function toArray( $authServices = NULL )
	{
		( is_string($authServices) && "" != $authServices )?
			$authServices = array($authServices): null;

		( is_null($authServices) )?
			$authServices = array_merge(self::$_info, self::getServices()): null;

		if (!is_array($authServices))
			throw new Zend_Exception('Input must be a string or an array.');
		$sessionData = array();
		foreach ($authServices as $authService)
		{
			$sessionData[$authService] = $this->_namespace->__get($authService);
		}
		return $sessionData;
	}

	/**
	 * Check if a user is signed in.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	bool
	 */
	public function hasUser()
	{
		return ( 	is_array($theUser = $this->_namespace->__get('user')) &&
					!empty($theUser))? true: false;
	}

	/**
	 * Return the user id if a user is signed in.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	null|int
	 */
	public function getUserId()
	{
		if ( $this->hasUser() )
		{
			$theUser = $this->_namespace->__get('user');
			return $theUser['userId'];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Return role ids.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	array
	 */
	public function getActiveRoleIds()
	{
		return $this->_namespace->privileges->getRoleIds();
	}

	/**
	 * Returns all services that are included.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	public function getServices()
	{
		return array_keys($this->_services);
	}

	/**
	 * Get login url for a specific service.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $serviceName
	 * @return	string
	 * @throws Zend_Exception if the service name is not a key in $_services.
	 */
	public function getLoginUrl($serviceName)
	{
		if(!$this->_isService($serviceName))
		{
			throw new Zend_Exception($serviceName. ' is not a valid service.');
		}
		return $this->_services[$serviceName]['object']->getLoginUrl();
	}

	/**
	 * By sending the service name and authentication ticket,
	 * this function handles:
	 * if the authentication is correct,
	 * ~ standard login if the account id is previosly stored,
	 * ~ adds account id to current user if account id is not previosly stored
	 *   and thereafter sets default privileges given service to the user,
	 * ~ returns 'logout' if the current user and the user of the user
	 *   connected to the login service account id does not conform,
	 * ~ returns 'notFound' if the account id is not previosly stored.
	 * ~ returns false if the authentication failed.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $serviceName
	 * @param	string $ticket
	 * @throws	Zend_Exception
	 * @return	bool|string
	 */
	public function serviceLogin($serviceName, $ticket)
	{
		if(!$this->_isService($serviceName))
		{
			throw new Zend_Exception($serviceName. ' is not a valid service.');
		}

		$sessionData = $this->_services[$serviceName]['object']->authenticate($ticket);
		if( isset($sessionData) && is_array($sessionData) && $sessionData['found'] )
		{
			if(!isset($this->_namespace->$serviceName) || !is_array($this->_namespace->$serviceName))
			{
				$this->_namespace->$serviceName = array();
			}
			// If no one is loged in, make a standard login.
			if(!$this->hasUser())
			{
				$this->_loadUsersTable();
				$userInfo = $this->_users->findUser($sessionData['userId']);
				$this->_setInfo($userInfo);
				$this->_setServiceUserInfo($serviceName, $sessionData[$serviceName]);
				$this->_getPrivileges();
				return true;
			}
			// Check if the user is is logged in already and that the new information conforms with the existing user.
			else if ( $this->hasUser() && !strcmp( $this->getUserId(), $userId = $sessionData['userId'] ))
			{
				// Allow the user to login with multiple accounts.
				$this->_setServiceUserInfo($serviceName, $sessionData[$serviceName]);
				$this->_getPrivileges();
				return true;
			}
			else
			{
				// Store the information so the new user does not have to log in again.
				if(!isset($this->_namespace->logoutLogin) || is_array($this->_namespace->logoutLogin))
				{
					$this->_namespace->logoutLogin = array();
				}
				$this->_namespace->logoutLogin = array($serviceName => $sessionData[$serviceName]);
				return 'logout';
			}
		}
		else if(isset($sessionData) && is_array($sessionData) && !$sessionData['found'])
		{
			if(!isset($this->_namespace->new) || is_array($this->_namespace->new))
			{
				$this->_namespace->new = array();
			}
			$this->_namespace->new = array($serviceName => $sessionData[$serviceName]);
			// New user Login.
			return 'notFound';
		}
		else
		{
			return false;
		}
		return $ticket;
	}

	/**
	 * Check if new info is stored.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	bool
	 */
	public function hasNew()
	{
		$services = $this->getServices();
		$diff = array_diff($newServiceLogin = array_keys($this->_namespace->new), $services);
		return ( is_array($diff) && empty($diff) && !empty($newServiceLogin) )? true:false;
	}

	/**
	 * Should be used when a service account id is not previosly stored and no one is logged in.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $serviceName
	 * @throws	Zend_Exception if no new info is stored in the namespace.
	 * @throws	Zend_Exception if the "new" service account id is already stored.
	 */
	public function addLoginServiceToUser( $serviceName )
	{
		// Make sure that new infomation is set and that this login service id is not stored already.
		if(!$this->hasNew($serviceName))
		{
			throw Zend_Exception('New info must be stored');
		}

		$serviceObj = $this->_services[$serviceName]['object'];
		$identifier = $this->_namespace->new[$serviceName];
		unset($this->_namespace->new[$serviceName]);
		if($serviceObj->getUserInfo($identifier, true))
		{
			throw new Zend_Exception('The service account id is already stored.');
		}
		// New information exists and it is not previosly stored.
		// If no one is currently loged in, create a new user.
		if ( null === $userId = $this->getUserId() )
		{
			$userId = $this->_createNewUser();
		}
		// Add service account and corresponding default privileges to user.
		$userInfo = $serviceObj->addAccount($userId, $identifier);
		$this->_setServiceUserInfo($serviceName, $userInfo);
		$serviceDefaultCategories = $serviceObj->getDefaultCategories();
		$this->_addDefaultPrivileges($userId, $serviceDefaultCategories);
		return $this->toArray();
	}

	/**
	 * Logout from a specific service. If $accountId is null, all accounts ar logged out.
	 * The logout url with given redirect is returned.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $serviceName
	 * @param	string|null $accountId
	 * @param	string $redirect
	 * @return	string logout url
	 * @throws	Zend_Exception if $serviceName is not an available service
	 */
	public function serviceLogout($serviceName, $accountId = null, $redirect = null)
	{
		if (!$this->_isService($serviceName))
			throw new Zend_Exception($serviceName.' is not a valid service');
		$serviceObj = $this->_services[$serviceName]['object'];
		if ( $serviceObj->isLoggedIn($this->_namespace->$serviceName) )
		{
			$logoutUrl = $serviceObj->logout($this->_namespace->$serviceName, $accountId, $redirect);;
		}
		else $logoutUrl = true;

		if ( !$this->hasServiceSignedIn() )
		{
			// If this was the last account that was logged in, remove the user from the namespace.
			$this->_userLogout();
		}
		return $logoutUrl;
	}

	/**
	 * To be able to logout from all accounts without click on every service logout button,
	 * this function will return a logoutUrl to a service that is logged in and clear the stored data for that service.
	 * The function will return true when no service has a account logged in or if a user is not set.
	 * For total logout should $redirect point back to the page that calls this function.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	public function logoutAllServices($redirect)
	{
		if ( !$this->hasUser() )
		{
			return true;
		}
		else
		{
			$logoutUrl = true;
			foreach ($this->_services as $serviceName => $serviceArray)
			{
				if ( $serviceArray['object']->isLoggedIn($this->_namespace->$serviceName) )
				{
					$logoutUrl = $this->serviceLogout($serviceName, null, $redirect);
					break;
				}
			}
			// If $logoutUrl is still true, it means that the user should not be signed in anymore.
			if ( true === $logoutUrl )
			{
				$this->_userLogout();
			}
			return $logoutUrl;
		}
	}

	/**
	 * #@-
	 */

	protected function _setServiceUserInfo( $serviceName, $userInfo )
	{
		$serviceHolder = &$this->_namespace->$serviceName;
		foreach ($userInfo as $accountId => $info)
		{
			$serviceHolder[$accountId] = $info;
		}
	}

	public function hasServiceSignedIn($specificServices = array(), $identifiers = array())
	{
		if( !is_array($specificServices) && !is_string($specificServices) )
			throw new Zend_Exception('$inputServices has to be in an array or as a string.');
		// If input is a string, redeclare it as an array.
		if( is_string($specificServices) )
			$specificServices = array($specificServices);
		// All requested services must be real services
		$diff = array_diff($specificServices, $this->getServices());
		if (!empty($diff))
		{
			$failedServices = implode(', ', $diff);
			throw new Zend_Exception('All requested services must be valid, following are not: '. $failedServices);
		}

		if( !is_array($identifiers) && !is_string($identifiers) && !is_null($identifiers) )
		throw new Zend_Exception('$identifiers has to be in an array or as a string.');
		// If input is a string or null, redeclare it as an array.
		if( is_string($identifiers) )
			$identifiers = array($identifiers);
		if ( is_null($identifiers) )
			$identifiers = array();

		$hasMoreAccountsLoggedIn = false;
		foreach ($this->_services as $serviceName => $serviceArray)
		{
			if ( $hasMoreAccountsLoggedIn = $serviceArray['object']->isLoggedIn($this->_namespace->$serviceName, $identifiers) )
				break;
		}
		return $hasMoreAccountsLoggedIn;
	}

	public function test()
	{

	}
}
