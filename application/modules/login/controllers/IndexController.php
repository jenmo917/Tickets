<?php

class Login_IndexController extends Zend_Controller_Action
{
	protected $_liuLogin;
	protected $_userInfoSession;

	/**
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	public function init()
	{
		$flash = $this->_helper->getHelper('flashMessenger');
		if ($flash->hasMessages())
		{
			$this->view->messages = $flash->getMessages();
		}
	}

	/**
	 *
	 * Redirects to cas action.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	public function indexAction()
	{
		$liuServiceName = Login_Model_LiuInfoSession::getServiceName();
		$this->_loadUserInfoSession();
		$this->view->cas_url
			= $this->_userInfoSession->getLoginUrl($liuServiceName);
		$this->liuLoginAction();
	}

	/**
	 *  Log in with LiU CAS.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 */
	public function liuLoginAction()
	{
		$liuServiceName = Login_Model_LiuInfoSession::getServiceName();
		$this->_loadUserInfoSession();
		$this->view->cas_url
			= $this->_userInfoSession->getLoginUrl($liuServiceName);
		$request = $this->getRequest();
		// A CAS request returns a ticket as a get variable. Do a redirect to fix the url.
		// TODO:: Make up a good way to set the cas service so the url does not include ticket.
		/*if ($ticket = $request->getQuery('ticket'))
		{
			$ticket = htmlspecialchars($ticket);
			$this->_redirect($this->_helper->url->url(
											array(	'module' => 'login',
													'controller' => 'index',
													'action' => 'liuLogin',
													'ticket' => $ticket),
													"defaultRoute",true));
		}*/
		// When the redirect is made, authenticate the user.
		if ( $ticket = htmlspecialchars($request->getParam('ticket')) )
		{
			if ($loginResult = $this->_userInfoSession->serviceLogin($liuServiceName, $ticket))
			{
				if ( true === $loginResult )
				{
					$request = $this->getRequest();

					if (null !== $redirect = $request->getParam('redirect'))
					{
						foreach ($redirect as $key => $value)
						{
							$url[$key] = $value;
						}
						$this->_redirect($this->_helper->url->url($url, "defaultRoute",true));
					}
				}
				// Show Register new user form.
				elseif(!strcmp('notFound', $loginResult))
				{
					// TODO: Translate!
					$mess  = "You are about to create a new user. ";
					$mess .= "To use our services, we need your permission to save your login service user id. ";
					$mess .= "We will never store your password, this is the beauty with using external login services.";
					$this->_helper->flashMessenger->addMessage($mess);
					$this->view->form = new Login_Form_Confirm();
					$this->view->message = array_merge
					(
						$this->_helper->flashMessenger->getMessages(),
						$this->_helper->flashMessenger->getCurrentMessages()
					);
					$this->_helper->flashMessenger->clearCurrentMessages();
				}
				else //TODO: $loginResult = 'logout', this is the case if a new user tries to login on an old users account.
				{
					//echo "<pre>";
						//var_dump($ticket);
					//echo "</pre>";
				}
			}
			else
			{
				// If the login failed, send the user back to the login page.
				$this->_helper->flashMessenger->addMessage("Log in failed.");
				$url = array(	'module' => 'login',
								'controller' => 'index',
								'action' => 'liu-login');
				$this->_redirect($this->_helper->url->url($url, "defaultRoute",true));
			}
		}
		// If a new user is about to register
		elseif (	$confirmation = htmlspecialchars($request->getParam('ok')))
		{
			if ( $this->_userInfoSession->hasNew() )
				$this->view->ticket = $this->_userInfoSession->addLoginServiceToUser($liuServiceName);
			else
				$this->_redirect('/');
		}

		//$this->view->ticket = $this->getRequest()->getParams();
	}

	/**
	 * This will logout the LiU account and redirect to $redirect.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param $redirect array
	 */
	public function liuLogoutAction()
	{
		$this->_loadUserInfoSession();
		$redirect = $this->getRequest()->getParam('redirect');
		$liuId = $this->getRequest()->getParam('liuId');
		echo "<pre>";
			var_dump('liuId', $liuId);
		echo "</pre>";

		// Get service names to later decide wheter to logout the user or not
		$liuServiceName = Login_Model_LiuInfoSession::getServiceName();
		if ($this->_userInfoSession->hasServiceSignedIn($liuServiceName, $liuId))
		{
			//$this->_helper->flashMessenger->addMessage("Your session on LiU's CAS has ended.");
			//$logoutUrl = $this->_userInfoSession->serviceLogout($liuServiceName, $liuId, $redirect);
			// Do the actual logout.
			$this->_redirect($logoutUrl);
		}
		else
		{
			//$this->_helper->flashMessenger->addMessage("You currently not logged in on LiU's CAS.");
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
		if ( $this->_userInfoSession->hasUser() )
		{
			$thisUrl = array('module' => 'login','controller' => 'index', 'action' => 'logout');
			$defaultUrl = array('module' => 'default','controller' => 'index', 'action' => 'index');
			$logoutUrl = $this->_userInfoSession->logoutAllServices($this->_helper->url->url($thisUrl, "defaultRoute",true));
		}

		( true === $logoutUrl )?
			null://$this->_redirect($this->_helper->url->url($defaultUrl,"defaultRoute",true)):
			$this->_redirect($logoutUrl);
	}

	public function testAction()
	{
		$this->_userInfoSession = new Login_Model_UserInfoSession();
		$this->_userInfoSession->test();
		//$this->_userInfoSession->test();
			echo "<pre>";
				var_dump($_SESSION);
			echo "</pre>";
		//$this->_redirect($this->_helper->url->url(array('module' => 'admin','controller' => 'event', 'action' => 'sell', 'event_id' => $params['event_id']),"defaultRoute",true));
	}

	protected function _loadUserInfoSession()
	{
		if ( !$this->_userInfoSession instanceof Login_Model_UserInfoSession )
		{
			$this->_userInfoSession = new Login_Model_UserInfoSession();
		}
	}
}
