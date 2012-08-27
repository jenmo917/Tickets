<?php
/**
 * Acl_Plugin_SecurityCheck
 *
 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
 */
class Acl_Plugins_SecurityCheck extends Zend_Controller_Plugin_Abstract
{
	const RESTRICTIVE = false;
	const RESTORE_ACL_FROM_CACHE = true;

	private $_controller;
	private $_module;
	private $_action;
	private $_userInfoSession;
	private $_acl;

	/**
	 * preDispatch
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 */
	public function preDispatch (Zend_Controller_Request_Abstract $request)
	{
		$this->_userInfoSession = new Login_Model_UserInfoSession();
		if ( !($this->_userInfoSession instanceof Login_Model_UserInfoSession) )
			throw new Zend_Exception('Failed to load Login_Model_UserInfoSession');

		// Get request parameters to be able to decide if the user is permitted to access the resource.

		$this->_module		= $request->getModuleName();
		$this->_controller	= $request->getControllerName();
		$this->_action		= $request->getActionName();

		// Get the Acl
		$this->_acl = Acl_Factory::get(!self::RESTORE_ACL_FROM_CACHE);
		if ( !($this->_acl instanceof Zend_Acl) )
			throw new Zend_Exception('Failed to load Zend_Acl');
		// Resources building blocks.
		$pagePrefix = Acl_Resources::PAGEPREFIX;
		$delimiter = Acl_Resources::DELIMITER;

		// Create the needels that permission is tried against.
		$resource =	$pagePrefix.$delimiter.$this->_module.$delimiter.
					$this->_controller.$delimiter.$this->_action;

		$userPermitted = $this->_acl->isAllowed(null, $resource, 'resourceStackCheck');

		if (!$userPermitted)
		{
			if (null === $request->getParam('redirect'))
			{
				$request->setParam('ticket', null);
				$request->setParam('redirect', $request->getParams());
			}
			$request->setModuleName('login');
			$request->setControllerName('index');
			$request->setActionName('index');
		}
		else
		{
			if (null !== $request->getParam('redirect'))
				$request->setParam('redirect', null);
		}
	}
}
