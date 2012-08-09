<?php
class Login_Model_LiuLogin
{
	/**
	 *	@var Acl_Db_Table_Users
	 */
	protected $_users;

	/**
	 * @var	Acl_Db_Table_LiuLogins
	 */
	protected $_userLiuLogins;

	/**
	 * @var	Acl_Db_Table_UserInfoSession
	 */
	protected $_userInfoSession;

	/**
	 * Get an Login_Model_UserInfoSession.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	public function __construct()
	{
		$this->_userInfoSession = new Login_Model_UserInfoSession();
	}

	/**
	 * Login and get privileges
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array $userData
	 * @return	boolean|string
	 */
	function userLogin( $userData )
	{
		$this->_users = new Acl_Db_Table_Users();
		$this->_userLiuLogins = new Acl_Db_Table_UserLiuLogins();
		// If the user is found, continue the login process.
		$userLiuLogin = $this->_userLiuLogins->findUser($userData['userName']);
		if ( $userLiuLogin )
		{
			// Check if someone already is in.
			if ( $this->_userInfoSession->hasUser() )
			{
				// Allow the user to login with multiple accounts.
				// Check if the account is registered on the current user.
				if ( !strcmp( 	$this->_userInfoSession->getUserId(),
								$userId = $userLiuLogin[$this->_userLiuLogins->fetchNew()->getColumnName('userId')] ) )
				{
					$this->_userInfoSession->setLiuInfo($userLiuLogin);
					//TODO: Fetch privileges.
					return true;
				}
				else
				{
					// TODO: The user must log out.
					// TODO: Make it possible to connect accounts.
					return 'logout';
				}
			}
			// New user Login.
			else
			{
				$this->_userInfoSession->setLiuInfo($userLiuLogin);
				//TODO: Fetch privileges.
				return true;
			}
		}
		else
		{
			return 'notFound';
		}
	}

	/**
	 * Redirect the logout to the user session.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v
	 * @param unknown_type $liuId
	 */
	function logout( $liuId = null )
	{
		return $this->_userInfoSession->liuLogout($liuId);
	}

	/**
	 * Create a new user and store the LiU ID, if this LiU ID is unique.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param string $liuId
	 */
	function registerNewUser( $liuId )
	{
		// Check if this Liu ID is already stored.
		$this->_userLiuLogins = new Acl_Db_Table_UserLiuLogins();
		$newLiuLogin = $this->_userLiuLogins->createRow();
		$liuIdColumn = $newLiuLogin->getColumnName('liuId');
		$select = $this->_userLiuLogins->select()->where($liuIdColumn.' LIKE ?', $liuId);
		// If it is an unique Liu Id, create a new user and save the LiU ID.
		if (	!($result = $this->_userLiuLogins->fetchRow($select))	)
		{
			$this->_users = new Acl_Db_Table_Users();
			$newUser = $this->_users->createRow();
			$newUser->save();
			$userId = $newUser->getColumn('userId');
			$newLiuLogin	->setColumn('userId', $userId)
							->setColumn('liuId', $liuId)
							->save();
			//TODO: Add permissions to the new user. Authenicated user and student etc. Do this via the db.
			return true;
		}
		else
		{
			return false;
		}
	}
}
