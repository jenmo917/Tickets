<?php
class Acl_LoginService_ReferenceFactory
{
	protected function __construct()
	{}
	
	public static function getReferenceStorage($serviceName)
	{
		switch ($serviceName)
		{
			case Login_Model_LiuInfoSession::getServiceName():
				return new Acl_Db_Table_UserLiuLogins();
			case Login_Model_OpenIdInfoSession::getServiceName():
				return new Acl_Db_Table_UserOpenIds();
			default:
				return null;
			break;
		}
	}
}