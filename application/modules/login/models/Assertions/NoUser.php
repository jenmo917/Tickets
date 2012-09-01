<?php
class Login_Model_Assertion_NoUser implements Zend_Acl_Assert_Interface
{
	function assert( Zend_Acl $acl, Zend_Acl_Role_Interface $role = null, Zend_Acl_Resource_Interface $resource = null, $privilege = null )
	{
		$uI = new Login_Model_UserInfoSession();
		return !$uI->hasUser();
	}
}