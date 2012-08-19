<?php
class Admin_Model_Assertion_UsersEvent implements Zend_Acl_Assert_Interface
{
	function assert( Zend_Acl $acl, Zend_Acl_Role_Interface $role = null, Zend_Acl_Resource_Interface $resource = null, $privilege = null )
	{
		$uI = new Login_Model_UserInfoSession();
		$eventId = Zend_Controller_Front::getInstance()->getRequest()->getParam('event-id');
		if ( null !== $eventId )
		{
			$settings = array();
			$settings['roleId'] = (null !== $role)? $role->getRoleId(): null;
			$settings['eventId'] = $eventId;
			$settings['active'] = null;

			if ( $uI->hasPrivilegeThatFollows($settings) )
				return true;
			else
				return false;
		}
		else
			return false;
	}
}