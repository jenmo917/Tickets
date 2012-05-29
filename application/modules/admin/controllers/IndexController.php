<?php

class Admin_IndexController extends Zend_Controller_Action
{
    
    public function indexAction()
    {
        // Fetch events
        $events = new Admin_Model_AdminEvents();
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $events = $events->fetchEvents();
        $this->view->events = $events;
        $this->view->messages = $flashMessenger->getMessages();
    }
    
    public function createEventAction()
    {
        $events = new Admin_Model_AdminEvents();
        
        // Get data
        $data = $this->_request->getParams();

        // Create form with one ticket type form
        $form = new Admin_Form_EventInfo();
        if(!isset($data['step2'])){
            $numOfTicketTypes = 1;
        }
        else
        {
            $numOfTicketTypes = COUNT($data['step2']);            
        }
        $form->create($numOfTicketTypes);
        $this->view->form = $form;
 
        if(isset($data['submit']) && $form->isValid($data))
        {
            // Remove submit from data
            unset($data['submit']);
            
            // Fix params (public is saved with the rest of the event info from step 1)
            $data['event'] = $data['step1'];
            $data['event']['public'] = $data['step3']['public'];
            
            // Save event
            $event = $events->saveEvent($data['event']);
            $flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $flashMessenger->addMessage($event->name.' skapades!');

            // Save ticket types
            foreach ($data['step2'] as $ticketTypeArray):
                // Save it if name is != ''
                if($ticketTypeArray['name'] != '')
                {
                    $ticketTypeArray['event_id'] = $event->event_id;
                    $events->saveTicketType($ticketTypeArray);
                }
            endforeach;
            $this->_redirect('/admin');
        }
    }
}