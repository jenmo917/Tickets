<?php
class Login_Model_LiuInfoSession implements Login_Model_UserInfoInterface
{
	const LIU_SERVICE_NAME = 'liu';
	/**
	 * #@+
	 * @access	protected
	 */
	protected $_auth;
	protected $_adapterCas;
	/**
	 * @var	Acl_Db_Table_LiuLogins
	 */
	protected $_userLiuLogins;
	static protected $_defaultPrivilegesCategories = array('liu-student');
	static protected $_casConfig = array	(
												'hostname'  => 'login.liu.se',
												'port'      => 443,
												'path'      => 'cas/',
											);
	/**
	 * #@-
	 */

	public function __construct()
	{
		$this->_auth	= Zend_Auth::getInstance();
		$this->_adapterCas = Login_Model_Adapter_Factory::getAdapter($this->getServiceName(), self::$_casConfig);
	}

	static public function getServiceName()
	{
		return self::LIU_SERVICE_NAME;
	}

	/**
	 * Structure a new LiU account for storage in user session.
	 * To be able to be logged in with multiple LiU accounts at the same time
	 * are the data structured as
	 * array(userId => $userId, $serviceName[$liuId] => array() and in this
	 * array are the created and updated values saved.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array $userLiuLogin
	 * @throws Zend_Exception if input is not array or does not contain right
	 * 			keys.
	 */
	protected function _structureInfo( $userLiuLogin )
	{
		// Get column names to be sure that the right data is fetched later on.
		$colNames = array( 'liuId'		=> Acl_Db_Table_UserLiuLogins::getColumnName('liuId'),
							'userId'	=> Acl_Db_Table_UserLiuLogins::getColumnName('userId'),
							'created'	=> Acl_Db_Table_UserLiuLogins::getColumnName('created'),
							'updated'	=> Acl_Db_Table_UserLiuLogins::getColumnName('updated'));

		if ( !is_array($userLiuLogin) )
		{
			throw new Zend_Exception('Input has to be an array.');
		}
		$diff = array_diff($colNames, array_keys($userLiuLogin));
		if ( !empty($diff) )
		{
			$keysString = implode(', ', $colNames);
			throw new Zend_Exception($keysString.' must be all be keys in input. Following was found: '.implode(', ', array_keys($userLiuLogin)));
		}

		// Structure the information.
		$structuredInfo
			= array(
						'userId' => $userLiuLogin[$colNames['userId']],
						self::getServiceName() =>
						array(	$userLiuLogin[$colNames['liuId']] =>
								array	(	$colNames['created'] => $userLiuLogin[$colNames['created']],
											$colNames['updated'] => $userLiuLogin[$colNames['updated']]
										)));
		return $structuredInfo;
	}

	/**
	 * Check if the user session has any LiU login.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	$serviceArray fetched from namespace with result of
	 * 			class::getServiceName() as key.
	 * @return	bool
	 */
	public function isLoggedIn(array $serviceArray, array $identifiers = array())
	{
		if ( is_array($serviceArray) && !empty($serviceArray))
		{
			if ( empty($identifiers) )
			{
				return true;
			}
			else
			{
				$diff = array_diff($identifiers, array_keys($serviceArray));
				return ( empty($diff) )? true:false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Logout from LiU Account.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $liuId
	 * @throws	Zend_Exception
	 */
	function logout( &$serviceArray, $liuId = null, $redirect = null )
	{
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? "https" : "http";
		$this->_adapterCas->setService(
						$protocol.'://'. $_SERVER['HTTP_HOST'].'/'.$redirect);
		$this->_adapterCas->setLogoutUrl();
		// If $liuId is not set to anything particular, clear the service array.
		if ( null === $liuId || !is_string($liuId) || !strcmp('', $liuId) )
		{
			$serviceArray = array();
		}
		else
		{
			if ( array_key_exists($liuId, $serviceArray) )
			{
				unset($serviceArray[$liuId]);
			}
			else
			{
				throw new Zend_Exception(
							'LiU-ID '. $liuId . ' is not currently logged in.');
			}
		}
		return $this->_adapterCas->getLogoutUrl();
	}

	/**
	 * Return the url where the login happens.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	string
	 */
	public function getLoginUrl()
	{
		return $this->_adapterCas->getLoginUrl();
	}

	public function authenticate($ticket)
	{
		$this->_adapterCas->setTicket($ticket);
		$result = $this->_auth->authenticate($this->_adapterCas);
		if ( $result->isValid() )
		{
			// Get the identity returned from the CAS
			$userData;
			if(is_array($this->_auth->getIdentity()))
				$userData = $this->_auth->getIdentity();
			else
				$userData = array(	'userName' => $this->_auth->getIdentity(), );
			
			$this->_auth->clearIdentity();

			// Get the user.
			$this->_loadLiuLoginTable();
			if(false !== $userLiuLogin = $this->getUserInfo($userData['userName']))
			{
				return array_merge(array('found' => true),$userLiuLogin);
			}
			else
			{
				return array_merge(array('found' => false, $this->getServiceName() => $userData['userName']));
			}
		}
		else
		{
			throw new Zend_Exception('not valid');
		}
	}

	/**
	 * Load the table where the Liu-IDs are connected to a user id.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	protected function _loadLiuLoginTable()
	{
		if(!$this->_userLiuLogins instanceof Acl_Db_Table_UserLiuLogins)
		{
			$this->_userLiuLogins = new Acl_Db_Table_UserLiuLogins();
		}
	}

	public function getUserInfo($id, $justBool = false)
	{
		$this->_loadLiuLoginTable();
		$rowClass = $this->_userLiuLogins->getRowClassName();
		if ( is_array($userInfo = $this->_userLiuLogins->findLiuId($id)) )
		{
			return ( $justBool )? true: $this->_structureInfo($userInfo);
		}
		else
		{
			return false;
		}
	}

	public function addAccount($userId, $identifier)
	{
		$this->_loadLiuLoginTable();
		$this->_userLiuLogins->createRow()
				->setColumn('userId', $userId)
				->setColumn('liuId', $identifier)
				->save();
		// Reload the user to get default values set by the storage mechanism.
		$userInfo = $this->_userLiuLogins->findLiuId($identifier);
		return $this->_structureInfo($userInfo);
	}

	static public function getDefaultCategories()
	{
		return self::$_defaultPrivilegesCategories;
	}
}
