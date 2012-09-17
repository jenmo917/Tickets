<?php

class Admin_IndexController extends Zend_Controller_Action
{
    /*
    * Overview of all events
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */
    public function indexAction()
    {
        // Create model
        $events = new Admin_Model_AdminEvents();
        // Get flashMessenger
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        // Fetch all events
        $events = $events->fetchEvents();
        // Assign the array to the view
        $this->view->events = $events;
        // Get messages
        $this->view->messages = $flashMessenger->getMessages();
    }
}
