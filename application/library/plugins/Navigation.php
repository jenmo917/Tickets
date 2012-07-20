<?PHP
class Plugins_Navigation extends Zend_Controller_Plugin_Abstract
{
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
                    'label'     => 'Start',
                    'module'    => 'default',
                    'controller'=> 'index',
                    'action'    => 'index'
                ),
                array(
                    'label'     => 'Create event',
                    'module'    => 'admin',
                    'controller'=> 'index',
                    'action'    => 'create-event',
                    'resource'  => 'users'
                ),
                array(
                    'label'     => 'My events',
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
                'label'     => 'Overview',
                'module'    => 'admin',
                'controller'=> 'event',
                'action'    => 'index',
                'params'    => array('event_id' => $params['event_id'])
            ),
            array(
                'label'     => 'Sell tickets',
                'module'    => 'admin',
                'controller'=> 'event',
                'action'    => 'sell',
                'params'    => array('event_id' => $params['event_id'])
            ),
            array(
                'label'     => 'Attendees',
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
