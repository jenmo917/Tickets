<?php
class Login_Model_LoginServiceFactory
{
	static protected $availableServices = 
			array(	'Login_Model_LiuInfoSession',
					'Login_Model_OpenIdInfoSession');
	
	protected function __construct()
	{}
	
	static public function getServices()
	{
		$services = array();
		foreach (self::$availableServices as $className)
		{
			$service = new $className();
			if ($service instanceof Login_Model_UserInfoInterface)
			{
				$services[$service->getServiceName()] =
					array(	'className'	=> $className,
							'object'	=> $service);
			}
			else
			{
				throw new Zend_Exception($className. ' must implement UserInfoInterface');
			}
		}
		return $services;
	}
}