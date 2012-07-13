<?php

class Admin_IndexController extends Zend_Controller_Action
{
    
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
    
    public function createEventAction()
    {
        // Create model
        $events = new Admin_Model_AdminEvents();
        
        // Get data
        $data = $this->_request->getParams();

        // Create form
        $form = new Admin_Form_EventInfo();
        
        // How many ticket type fieldsets to view in the form
        if(!isset($data['step2'])){
            $numOfTicketTypes = 1;
        }
        else
        {
            // Fix array so it starts with [0], [1], [2],..
            // jQuery in the form is the problem.
            $temp = array();
            // Reset order so it starts from 0.
            $i = 0;
            foreach($data['step2'] as $entry):
                $entry['order'] = $i;
                $temp[] = $entry;
                $i++;
            endforeach; 
            $data['step2'] = $temp;
        
            // How many ticket types to loop through?
            $numOfTicketTypes = COUNT($data['step2']);
        }
        // Create form
        $form->create($numOfTicketTypes);
        
        // Assign form to view
        $this->view->form = $form;
        
        // If form is valid
        if(isset($data['submit']) && $form->isValid($data))
        {
            // Remove submit from data
            unset($data['submit']);
            
            // Fix params (public is saved with the rest of the event info from step 1)
            $data['event'] = $data['step1'];
            $data['event']['public'] = $data['step3']['public'];
            
            // Save event
            $event = $events->saveEvent($data['event']);
            
            // Add message
            $flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $flashMessenger->addMessage($event->name.' skapades!');

            // Save ticket types
            foreach ($data['step2'] as $ticketTypeArray):
                // Save it if name is != ''
                if($ticketTypeArray['name'] != '')
                {
                    // Set event_id
                    $ticketTypeArray['event_id'] = $event->event_id;
                    // Save ticket type
                    $events->saveTicketType($ticketTypeArray);
                }
            endforeach;
            $this->_redirect($this->_helper->url->url(array('module' => 'admin'),null, true));
        }
    }
}