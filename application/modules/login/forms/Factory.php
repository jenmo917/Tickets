<?php
class Login_Form_Factory
{
	protected function __construct()
	{	}
	
	public static function getForm($serviceName, $options = array())
	{
		switch ($serviceName) {
			case Login_Model_OpenIdInfoSession::OPENID_SERVICE_NAME:
				return new Login_Form_OpenIdLogin($options);
			case Login_Model_LiuInfoSession::LIU_SERVICE_NAME:
				return new Login_Form_LiuLogin($options);
			default:
				return null;
		}
	}
}