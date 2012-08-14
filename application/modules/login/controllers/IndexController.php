<?php

class Login_IndexController extends Zend_Controller_Action
{
	protected $_options;
	protected $_adapterCas;
	protected $_liuLogin;
	protected $_userInfoSession;

	/**
	 * Settings for cas.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	public function init()
	{
		$this->_options = $this->getInvokeArg('bootstrap')->getOptions();
		$this->_auth	= Zend_Auth::getInstance();

		$flash = $this->_helper->getHelper('flashMessenger');
		if ($flash->hasMessages())
		{
			$this->view->messages = $flash->getMessages();
		}

		$config = array	(
							'hostname'  => 'login.liu.se',
							'port'      => 443,
							'path'      => 'cas/',
						);

		$this->_adapterCas = new Zend_Auth_Adapter_Cas($config);

		$this->view->cas_link = $this->_adapterCas->getLoginUrl();
	}

	/**
	 *
	 * Redirects to cas action.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	public function indexAction()
	{
		$this->casAction();
	}

	/**
	 *  Log in with LiU CAS.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	public function casAction()
	{
		// A CAS request returns a ticket.
		if ($this->getRequest()->getQuery('ticket'))
		{
			// Authenitcate the user.
			$this->_adapterCas->setTicket(htmlspecialchars($this->getRequest()->getQuery('ticket')));
			$result = $this->_auth->authenticate($this->_adapterCas);
			if(!$result->isValid())
			{
				// If the login failed, send the user back to the login page.
				$this->_helper->flashMessenger->addMessage("Log in failed.");
				$this->_redirect('/login');
			}
			else
			{
				// Get the identity returned from the CAS
				$userData;
				if(is_array($this->_auth->getIdentity()))
				{
					$userData = $this->_auth->getIdentity();
				}
				else
				{
					$userData = array(	'userName' => $this->_auth->getIdentity(), );
				}

				// Get the user.
				$this->_liuLogin = new Login_Model_LiuLogin();
				$loginStatus = $this->_liuLogin->userLogin($userData);

				if( true === $loginStatus)
				{
					//TODO: Set permissions.
					//$this->_redirect('/login/index/test');
				}
				elseif ( !strcmp('logout', $loginStatus) )
				{
					$this->_liuLogin->clearSession();
					$this->_liuLogin->userLogin($userData);
					$this->_redirect('/');
				}
				else
				{
					$mess  = "You are about to create a new user. ";
					$mess .= "To use our services, we need your permission to save your LiU-ID. ";
					$this->_helper->flashMessenger->addMessage($mess);
					$this->view->form = new Login_Form_Confirm();
					$this->view->message = array_merge
					(
						$this->_helper->flashMessenger->getMessages(),
						$this->_helper->flashMessenger->getCurrentMessages()
					);
					$this->_helper->flashMessenger->clearCurrentMessages();
					// TODO: New user. Register and let the user accept our terms.
					// TODO: The user shall be able to add this account to another account as well, if the user wants of course.
					// TODO: Redirect to logout action if someone else is in.
				}
			}
		}
		// Check if the new user allows us to store information.
		elseif( $this->getRequest()->getQuery('ok')	)
		{
			$userData;
			if(is_array($this->_auth->getIdentity()))
			{
				$userData = $this->auth->getIdentity();
			}
			else
			{
				$userData = array(	'userName' => $this->_auth->getIdentity(), );
			}
			$this->_liuLogin = new Login_Model_LiuLogin();
			if($this->_liuLogin->userLogin($userData) === true)
			{
				//TODO: Set permissions.
				$this->_redirect('/');
			}
			else
			{
				$newUserName = $this->_auth->getIdentity();
				if($this->_liuLogin->registerNewUser($newUserName))
				{
					$this->_redirect('/');
				}
			}
		}
		elseif( $this->getRequest()->getQuery('cancel')	)
		{
			$this->_auth->getStorage()->clear();
			$this->_redirect('/');
		}

		// Send to CAS for authentication
		if(!$this->_auth->hasIdentity())
		{
			$this->_redirect($this->_adapterCas->getLoginUrl());
		}
	}

	/**
	 * This will logout all LiU accounts and will redirect to applicationname/$redirect.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param $redirect string
	 */
	public function liuLogoutAction($redirect = null)
	{
		$this->_liuLogin = new Login_Model_LiuLogin();
		if ($this->_liuLogin->logout())
		{
			$this->_helper->flashMessenger->addMessage("Your session on LiU's CAS is now ended.");
			$protocol = $_SERVER['HTTPS'] ? "https" : "http";
			$this->_auth->clearIdentity();
			$this->_adapterCas->setService($protocol.'://'. $_SERVER['HTTP_HOST'].'/'.$redirect);
			$this->_adapterCas->setLogoutUrl();
			$this->_redirect($this->_adapterCas->getLogoutUrl());
			$this->view->didLogout = true;
			$this->_userInfoSession->hasLiuLogin() ? null: $this->_userInfoSession->userLogout();
		}
		else
		{
			$this->view->didLogout = false;
		}
	}

	/**
	 * Logout user.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	public function logoutAction()
	{
		$this->_userInfoSession = new Login_Model_UserInfoSession();
		$this->_userInfoSession->hasLiuLogin() ? $this->_userInfoSession->liuLogout() : null;
		$this->_userInfoSession->userLogout();
	}

	public function testAction()
	{
		$this->_userInfoSession = new Login_Model_UserInfoSession();
		$this->view->userInfo = $this->_userInfoSession->toArray();
		$this->_userInfoSession->test();
	}
}
