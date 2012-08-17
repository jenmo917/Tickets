<?php
//TODO: Allowed by assertions.
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

        // Get request parameters to be able to decide if the user is permitted to access the resource.
        $this->_module		= $this->getRequest()->getModuleName();
        $this->_controller	= $this->getRequest()->getControllerName();
        $this->_action		= $this->getRequest()->getActionName();

        // Get the Acl
        $this->_acl = Acl_Factory::get(!self::RESTORE_ACL_FROM_CACHE);
        $pagePrefix = Acl_Resources::PAGEPREFIX;
        $delimiter = Acl_Resources::DELIMITER;

        // Create the needels that permission is tried against.
        $resourceNeedels
        	= array	(
        		'all'			=> $pagePrefix. $delimiter. '*' .				$delimiter . '*' . 					$delimiter . '*',
        		'module'		=> $pagePrefix. $delimiter. $this->_module . 	$delimiter . '*' . 					$delimiter . '*',
        		'controller'	=> $pagePrefix. $delimiter. $this->_module . 	$delimiter . $this->_controller . 	$delimiter . '*',
        		'action'		=> $pagePrefix. $delimiter. $this->_module . 	$delimiter . $this->_controller . 	$delimiter . $this->_action
        			);

        $userPermitted = $this->_isAllowed($resourceNeedels);

        if (!$userPermitted) {
            $request->setParam('redirect', $request->getParams());
            $request->setModuleName('login');
            $request->setControllerName('index');
            $request->setActionName('index');
        }
    }

    /**
     * By going from the least specified to the most specified level in access, it is decided if the user is granted access.
     * By inheritance allows and denys can alter and these are set as true and false respectively.
	 * If a rule is not found at one level, that key will be null.
     *
     * @param	array	$resourceNeedels
     * @return bool
     */
    private function _isAllowed($resourceNeedels)
    {
    	if ( !($this->_userInfoSession instanceof Login_Model_UserInfoSession) )
    	{
    		throw new Zend_Exception('Failed to load Login_Model_UserInfoSession');
    	}
    	if ( !($this->_acl instanceof Zend_Acl) )
    	{
    		throw new Zend_Exception('Failed to load Zend_Acl');
    	}

    	// Initiate the access result array.
    	$levels = array_keys($resourceNeedels);
    	$access = array	();
    	foreach ( $levels as $level)
    	{
    		$access[$level] = null;
    	}

    	// Get role ids from user privileges and start evaluate access on different levels.
    	$roleIds = $this->_userInfoSession->getActiveRoleIds();
    	foreach ($resourceNeedels as $level => $resource)
    	{
    		if ( $this->_acl->has($resource) )
    		{
    			foreach ($roleIds as $roleId)
    			{
    				if ( $this->_acl->hasRole($roleId) )
    				{
    					// Check if the role has deny set for this resource.
    					if($denied	= $this->_acl->isAllowed($roleId, $resourceNeedels[$level], 'deny'))
    					{
    						$access[$level] = false;
    					}
    					// Check if the role has allow set for this resource.
    					if($allowed = $this->_acl->isAllowed($roleId, $resourceNeedels[$level], 'allow'))
    					{
    						$access[$level] = true;
    					}
    					// If the user is allowed, break this level search.
    					if ( true === $access[$level] )
    					{
    						break;
    					}
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
}
