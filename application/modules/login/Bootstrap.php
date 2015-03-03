<?php

class Login_Bootstrap extends Zend_Application_Module_Bootstrap
{
	protected function _initAutoload () {
		 //configure new autoloader
		 $autoloader = new Zend_Application_Module_Autoloader (array ('namespace' => 'Login', 'basePath' => APPLICATION_PATH."/modules/login"));
		 // autoload validators definition
		//$autoloader->addResourceType ('Validate', 'validators', 'Validate_');
		$autoloader->addResourceType ('Plugin', 'plugins', 'Plugin');
	}
}
