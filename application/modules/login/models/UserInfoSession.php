<?php
//TODO: Auto logout a user after AUTO_LOGOUT time.
class Login_Model_UserInfoSession
{
	static protected $_services = array('user', 'liu', 'privileges');
	protected $_namespace;
	const USER_INFO_NS = 'Attend_UserInfo';
	const AUTO_LOGOUT	= 900; // 15 minutes.

	/**
	 * Sets the namespace and fetches user privileges.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	function __construct()
	{
		// Try to fetch an old instance of this namespace USER_INFO_NS.
		if ( Zend_Registry::isRegistered(self::USER_INFO_NS) )
		{
			$this->_namespace = Zend_Registry::get(self::USER_INFO_NS);
		}
		else
		{
			$this->_namespace = new Zend_Session_Namespace(self::USER_INFO_NS, true);
			Zend_Registry::set(self::USER_INFO_NS, $this->_namespace);
		}

		if ( !isset($this->_namespace->user) )
		{
			$this->_namespace->user = null;
		}

		//Get all privileges for this user.
		$this->getPrivileges();
	}

	/**
	* Set user id, create timestamp and update timestamp to session.
	* @author	Daniel Josefsson <dannejosefsson@gmail.com>
	* @since	v0.1
	* @param int $userId
	 */
	private function setUserInformation( $userId )
	{
		$users = new Acl_Db_Table_Users();
		// Get an array over the user data for the given user id.
		$userData = $users->find((int) $userId)->current()->toArray();

		// Set variables.
		if ( !is_array($this->_namespace->user) )
		{
			$this->_namespace->user = array();
		}
		//TODO: CHECK!
		if ( isset($this->_namespace->user['userId']) && $this->_namespace->user['userId'] !== (int) $userId )
		{
			if ( isset($this->_namespace->privileges) && is_array($this->_namespace->privileges) )
			{
				$this->_namespace->__unset('privileges');
				$this->_namespace->privileges = array();
			};
		}
		$this->_namespace->user['userId']	= (int) $userId;
		$this->_namespace->user['created']	= $userData[$users->getColumnName('created')];
		$this->_namespace->user['updated']	= $userData[$users->getColumnName('updated')];
	}

	/**
	 * Store a new LiU account into the user session.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param array $userLiuLogin
	 */
	function setLiuInfo( $userLiuLogin )
	{
		// Get column names to be sure that the right data is fetched later on.
		$liuIdColName = Acl_Db_Table_UserLiuLogins::getColumnName('liuId');
		$userIdColName = Acl_Db_Table_UserLiuLogins::getColumnName('userId');

		if ( !is_array($this->_namespace->liu) )
		{
			$this->_namespace->liu = array();
		}
		// To be able to be logged in with multiple LiU accounts at the same time are the data stored in session as
		// liu["LiU-ID"] and in this array are the created and updated values saved.
		$this->_namespace->liu[$userLiuLogin[$liuIdColName]]
							= array(	'created' => $userLiuLogin[Acl_Db_Table_UserLiuLogins::getColumnName('created')],
										'updated' => $userLiuLogin[Acl_Db_Table_UserLiuLogins::getColumnName('updated')]);
		// Set the user information
		$this->setUserInformation($userLiuLogin[$userIdColName]);
		$this->getPrivileges();
	}


	/**
	 * Check if the user session has any LiU login.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	bool
	 */
	function hasLiuLogin()
	{
		if ( $this->_namespace->__isset('liu') && is_array($this->_namespace->liu) && !empty($this->_namespace->liu))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Logout from LiU Accounts. If a specific username is given, sign out this LiU account.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $liuId
	 * @throws	Zend_Exception
	 */
	function liuLogout( $liuId = null )
	{
		if ( $this->hasLiuLogin() )
		{
			if ( $liuId )
			{
				if ( array_key_exists($liuId, $this->_namespace->liu) )
				{
					unset($this->_namespace->liu->$liuId);
				}
				else
				{
					throw new Zend_Exception('LiU-ID '. $liuId . ' is not currently logged in.');
				}
			}
			else
			{
				unset($this->_namespace->liu);

			}
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	* Totally sign a user out.
	* @author	Daniel Josefsson <dannejosefsson@gmail.com>
	* @since	v0.1
	* @return	bool
	 */
	function userLogout()
	{
		$this->_namespace->privileges->clear();
		$this->_namespace->unsetAll();
		return true;
	}

	/**
	 * Return this namespace as an array.
	 * If $authServices is left empty, the whole namespace will be returned.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array|string	$authServices
	 * @return	array
	 */
	function toArray( $authServices = NULL )
	{
		( is_string($authServices) && "" != $authServices )? $authServices = array($authServices): null;

		( is_null($authServices) )? $authServices = self::$_services: null;

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
	function hasUser()
	{
		return ( is_array($theUser = $this->_namespace->__get('user')) && !empty($theUser))? true: false;
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
	 * Get privileges for current user. Privileges is set to USER_INFO_NS->privileges.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @uses	Acl_Privileges
	 */
	private function getPrivileges()
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

		$privilegesTable = new Acl_Db_Table_Privileges();
		$userPrivileges = $privilegesTable->getPrivilegesForUserId($userId, true);
		$validPermissionIds = array();
		foreach ($userPrivileges as $userPrivilege)
		{
			$this->_namespace->privileges->addPrivilege(new Acl_Privilege($userPrivilege));
			$validPermissionIds[] = $userPrivilege->getColumn('privilegeId');
		}
		$this->_namespace->privileges->sanitize($validPermissionIds);
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

	public function test()
	{
		echo "<pre>";
			//var_dump("user",$this->getUserId());
		echo "</pre>"; //$this->_privileges
	}
}
