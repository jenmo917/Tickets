<?php
class Login_Model_Adapter_Factory
{
	protected function __construct()
	{}
	
	static public function getAdapter($serviceName, $options = array())
	{
		switch ($serviceName) {
			case Login_Model_OpenIdInfoSession::OPENID_SERVICE_NAME:
				return new Login_Model_Adapter_OpenId($options);
			case Login_Model_LiuInfoSession::LIU_SERVICE_NAME:
				return new Zend_Auth_Adapter_Cas($options);
			default:
				throw new Zend_Exception($serviceName . ' is not a valid service name.');
		}
		return $services;
	}
}