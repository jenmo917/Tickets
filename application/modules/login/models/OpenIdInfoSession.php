<?php
class Login_Model_OpenIdInfoSession implements Login_Model_UserInfoInterface
{
	const OPENID_SERVICE_NAME = 'openId';
	/**
	 * #@+
	 * @access	protected
	 */
	protected $_auth;
 	protected $_adapterOpenId;
	/**
	 * @var	Acl_Db_Table_LiuLogins
	 */
	protected $_userLiuLogins;
	/**
	 * @var	object Storage for user => openId references.
	 */
	protected $_userOpenIdReferences;
	static protected $_defaultPrivilegesCategories = array('openId-user');
	/**
	 * #@-
	 */

	public function __construct()
	{
		$this->_loadStorage();
		$this->_auth = Zend_Auth::getInstance();
		$this->_adapterOpenId = Login_Model_Adapter_Factory::getAdapter($this->getServiceName());
	}

	/**
	 * Returns the service name.
	 * @return	string
	 */
	static public function getServiceName()
	{
		return self::OPENID_SERVICE_NAME;
	}
	
	/**
	 * Returns service specific login form. At least action should be set in options.
	 * @param array $options
	 * @return Login_Form_OpenIdLogin
	 */
	static public function getLoginForm(array $options = array())
	{
		return Login_Form_Factory::getForm(self::OPENID_SERVICE_NAME, $options);
	}

	/**
	 * Structure a new openId account for storage in user session.
	 * To be able to be logged in with multiple open id accounts at the same time
	 * ,the data are structured as
	 * array(userId => $userId, $serviceName[$liuId] => array() and in this
	 * array are the created and updated values saved.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array $openIdLogin
	 * @throws Zend_Exception if input is not array or does not contain right
	 * 			keys.
	 */
	protected function _structureInfo( $openIdLogin )
	{
		// Get column names to be sure that the right data is fetched later on.
		/*$colNames = array( 'liuId'		=> Acl_Db_Table_UserLiuLogins::getColumnName('liuId'),
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
		return $structuredInfo;*/
	}

	/**
	 * Check if the user session has any openId signed in.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	$serviceArray fetched from namespace with result of
	 * 			class::getServiceName() as key.
	 * @return	bool
	 * @todo	Make it fit with the data structure. 
	 */
	public function isLoggedIn(array $serviceArray = array(), array $identifiers = array())
	{
		if ( !empty($serviceArray) )
		{
			// If no identifiers is set, it is enough that the service array contains data.
			if ( empty($identifiers) )
			{
				return true;
			}
			// If some identifiers are set, find out if some of these are signed in.
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
	 * Sign out from openId account.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $openId
	 * @throws	Zend_Exception
	 * @todo	Make it fit with data structure.
	 */
	function logout( &$serviceArray, $openId = null, $redirect = null )
	{
		/*$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? "https" : "http";
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
		return $this->_adapterCas->getLogoutUrl();*/
	}

	/**
	 * Return the url where the sign in happens.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	string
	 * @todo	Fix url.
	 */
	public function getLoginUrl()
	{
		return "";//$this->_adapterCas->getLoginUrl();
	}

	public function authenticate($identifier)
	{
		$this->_adapterOpenId->setIdentifier($identifier);
		$result = $this->_auth->authenticate($this->_adapterOpenId);
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
	 * Load the storage where the openId's are connected to a user id.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	protected function _loadStorage()
	{
		if(!$this->_userOpenIdReferences instanceof Acl_LoginService_ReferenceInterface)
		{
			$this->_userOpenIdReferences = Acl_LoginService_ReferenceFactory::getReferenceStorage($this->getServiceName());
		}
	}

	public function getUserInfo($id, $justBool = false)
	{
		/*$this->_loadLiuLoginTable();
		$rowClass = $this->_userLiuLogins->getRowClassName();
		if ( is_array($userInfo = $this->_userLiuLogins->findLiuId($id)) )
		{
			return ( $justBool )? true: $this->_structureInfo($userInfo);
		}
		else
		{
			return false;
		}*/
	}

	public function addAccount($userId, $identifier)
	{
		$this->_loadStorage();
		$storage = $this->_userOpenIdReferences->storeAccount($userId, $identifier);
		// Reload the user to get default values set by the storage mechanism.
		$userInfo = $this->_userOpenIdReferences->findLiuId($identifier);
		return $this->_structureInfo($userInfo);
	}

	static public function getDefaultCategories()
	{
		return self::$_defaultPrivilegesCategories;
	}
}
