<?PHP
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /*
    * Add custum validator support
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */
	protected function _initAutoload () {
		// configure new autoloader
		$autoloader = new Zend_Application_Module_Autoloader (array ('namespace' => 'Admin', 'basePath' => APPLICATION_PATH."/modules/admin"));

		// autoload validators definition
		$autoloader->addResourceType ('Validate', 'validators', 'Validate_');
	}
}
