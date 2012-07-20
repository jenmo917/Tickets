<?php

class Default_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $eventModel = new Default_Model_Events();
        $events = $eventModel->fetchPublishedEvents();
        $this->view->events = $events;
    }


}

