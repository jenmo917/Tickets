<?PHP
class Plugins_Navigation extends Zend_Controller_Plugin_Abstract
{
    /**
    * Instance of Zend_Translate
    * @var Zend_Translate $_translator
    */       
    protected $_translate;
    
    /*
    * Get the top Level menu.
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	array
    */
    public function getTopLevelMenu()
    {
        $pages = array(
                array(
                    'label'     => $this->_translate->translate('Start'),
                    'module'    => 'default',
                    'controller'=> 'index',
                    'action'    => 'index'
                ),
                array(
                    'label'     => $this->_translate->translate('Create Event'),
                    'module'    => 'admin',
                    'controller'=> 'index',
                    'action'    => 'create-event',
                    'resource'  => 'users'
                ),
                array(
                    'label'     => $this->_translate->translate('My Events'),
                    'module'    => 'admin',
                    'controller'=> 'index',
                    'action'    => 'index',
                    'resource'  => 'users'
                )
            );

        return $pages;
    }

    /*
    * Get the event menu.
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	array
    */  
    public function getEventMenu($params)
    {
       $pages = array(
            array(
                'label'     => $this->_translate->translate('Overview'),
                'module'    => 'admin',
                'controller'=> 'event',
                'action'    => 'index',
                'params'    => array('event_id' => $params['event_id'])
            ),
            array(
                'label'     => $this->_translate->translate('Sell Tickets'),
                'module'    => 'admin',
                'controller'=> 'event',
                'action'    => 'sell',
                'params'    => array('event_id' => $params['event_id'])
            ),
            array(
                'label'     => $this->_translate->translate('Attendees'),
                'module'    => 'admin',
                'controller'=> 'event',
                'action'    => 'attendees',
                'params'    => array('event_id' => $params['event_id'])
            )

        );
        return $pages;
    }
    
    /*
    * Init menus
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	array
    */  
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {   
        
        $this->_translate = Zend_Registry::get('Zend_Translate');
        
        // get params
        $params = $request->getParams();
        // Always view the top level menu
        $topLevelMenu = $this->getTopLevelMenu();
        // Create menu container
        $topLevelMenu   = new Zend_Navigation(new Zend_Config($topLevelMenu));
        // Save menu in registry
        Zend_Registry::set('topLevelMenu',$topLevelMenu);

        // View event menu if controller is equal to event
        if($params['controller'] == 'event')
        {
            // Get menu
            $eventMenu = $this->getEventMenu($params);
            // Create menu container
            $eventMenu   = new Zend_Navigation(new Zend_Config($eventMenu));
            // Save menu in registry
            Zend_Registry::set('verticalMenu',$eventMenu);            
        }
    }   
}
?>
