<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /*
    * Init default mail transport
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */
    protected function _initMail()
    {
        $tr = new Zend_Mail_Transport_Smtp("smtp.gmail.com",
			array(
				"auth" => "login",
				"username" => "xxx",
				"password" => "xxx",
				"ssl"=>"ssl"
			)
		);
        Zend_Mail::setDefaultTransport($tr);
    }

    /*
    * Init doctype and push it to the layout view
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */
    protected function _initDoctype()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $view->doctype('HTML5');
    }

    /*
    * Init javascript file to be included in the header
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */
    protected function _initFiles()
    {
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $view->headScript()->appendFile('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js')
                ->appendFile('http://code.jquery.com/ui/1.8.20/jquery-ui.min.js')
                ->appendFile('/js/jquery.tablesorter.min.js')
                ->appendFile('/js/site.js')
                ->appendFile('/js/jquery-ui-timepicker-addon.js')
                ->appendFile('/js/date.js')
                ->appendFile('/js/jquery.livequery.js');
    }

    /*
    * Control routing and give the site url: sitename/lang/module/controller/action/param/value/../../
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */
    public function _initRoutes()
    {
        $this->bootstrap('FrontController');
        $this->_frontController = $this->getResource('FrontController');
        $router = $this->_frontController->getRouter();

        // Regex to check the language. xx-xx and xx is supported.
        $langRoute = new Zend_Controller_Router_Route_Regex(
            '([a-z]{2}(-[a-z]{2})*)',
                array(
                    'lang' => 'sv'
                ),
                array(
                    1 => 'lang',
                    2 => 'lang-country-void'
                ),
                '%s'
        );

        // The first parameter in the Zend_Controller_Router_Route constructor is a route definition that will be matched to a URL.
        // Route definitions consist of static and dynamic parts separated by the slash ('/') character.
        $defaultRoute = new Zend_Controller_Router_Route(
            ':module/:controller/:action/*',
            array(
                'module'=>'default',
                'controller'=>'index',
                'action'=>'index'
            )
        );

		$eventRoute = new Zend_Controller_Router_Route(
			'event/:eventname',
			array(
				'module' => 'default',
				'controller' => 'index',
				'action'=>'overview',
				'eventname'=>''
			)
		);

        // When chaining routes together, the parameters of the outer route have a higher priority than the parameters of the inner route.
        // When chaining routes together, their separator is a slash by default.
        $defaultRoute = $langRoute->chain($defaultRoute);
        $eventRoute = $langRoute->chain($eventRoute);

        // Add route to router
        $router->addRoute('defaultRoute', $defaultRoute);
        $router->addRoute('eventRoute', $eventRoute);
    }

	/**
	 * Cache initialization.
	 * @author		Daniel Josefsson <dannejosefsson@gmail.com>
	 * @version	v0.1
	 * @since		v0.1
	 */
	protected function _initCache()
	{
		$frontend = array	(	'lifetime' => 300,
								'automatic_seralization' => true
							);

		$backend = array	(	'cache_dir' => APPLICATION_PATH.'/cache/',);

		$cache = Zend_Cache::factory( 'Core', 'File', $frontend, $backend );

		Zend_Registry::set('AclCache', $cache);
	}

	protected function _initDb()
	{
		$config = $this->getOptions();

		$db = Zend_Db::factory($config['resources']['db']['adapter'], $config['resources']['db']['params']);

		//set default adapter
		Zend_Db_Table::setDefaultAdapter($db);
	}

	protected function _initAutoload () {
		// configure new autoloader
		$autoloader = new Zend_Application_Module_Autoloader (array (	'namespace' => 'Admin',
																		'basePath' => APPLICATION_PATH."/modules/admin"));

		// autoload validators definition
		//$autoloader->addResourceType ('Validate', 'validators', 'Validate_');
		$autoloader->addResourceType ('Assertion', 'models/Assertions', 'Model_Assertion');
		// configure new autoloader

		$autoloaderLogin = new Zend_Application_Module_Autoloader (array (	'namespace' => 'Login',
				'basePath' => APPLICATION_PATH."/modules/login"));
		// autoload validators definition
		$autoloaderLogin->addResourceType ('Assertion', 'models/Assertions', 'Model_Assertion');
		$autoloaderLogin->addResourceType('Adapter', 'models/Adapters', 'Model_Adapter');
	}
	protected function _initAclNavigation()
	{
		$acl = Acl_Factory::get();
		$view = $this->bootstrap('layout')->getResource('layout')->getView();
		$view->navigation()->setAcl($acl)->setRole(null);
		$pre = Acl_Resources::PAGEPREFIX;
		$del = Acl_Resources::DELIMITER;
	}

	protected function _initLayoutVars()
	{
		$layout= Zend_Controller_Action_HelperBroker::getStaticHelper('Layout');
		$layout->assign('currentRoute', $this->_frontController->getRouter());
	}
}
