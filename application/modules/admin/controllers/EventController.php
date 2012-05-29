<?php

class Admin_EventController extends Zend_Controller_Action
{   
    public function indexAction()
    {
        // Fetch event
        $events = new Admin_Model_AdminEvents();
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $event = $events->getEvent($_GET['event_id']);
        $this->view->event = $event;
        $this->view->messages = $flashMessenger->getMessages();
    }
    
    public function editAction()
    {
        // Initiate vars/objects.
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        
        // FIX: Get params??
        $post = $this->getRequest()->getPost();
        $get  = $this->getRequest()->getQuery();
 
        // Initiate model
        $events = new Admin_Model_AdminEvents();
               
        // Fix between post and get vars.
        if(isset($post['event_id']))
        {
            $eventId = $post['event_id'];
        }
        elseif(isset($post['event_id']))
        {
            $eventId = $post['event_id'];
        }
        elseif(isset($get['event_id']))
        {
            $eventId = $get['event_id'];
        }

        // Fetch event
        $event = $events->getEvent($eventId);
        // Fetch ticket types
        $ticketTypes = $events->getTicketTypes($eventId);
        
        // Create form with correct number of ticket types
        if(isset($post['submit']))
        {
            $numOfTicketTypes = COUNT($post['step2']);
        }
        else
        {
            $numOfTicketTypes = COUNT($ticketTypes);            
        }
        
        // Create form
        $form = new Admin_Form_EventInfo();
        
        // Create at least one ticket type form
        if($numOfTicketTypes == 0){ $numOfTicketTypes = 1; }
        $form->create($numOfTicketTypes);
        
        if(isset($post['submit']) && $form->isValid($post))
        {
            // Prepare data
            $eventData = $post['step1'];
            $eventData['event_id'] = $eventId;
            $eventData['public']   = $post['step3']['public'];
            $ticketTypeData = $post['step2'];
            
            // Save event
            $events->saveEvent($eventData);
            
            // Set message
            $flashMessenger->addMessage('Eventet har uppdaterats!');

            // Save ticket types
            foreach ($ticketTypeData as $ticketTypeArray):

                $ticketTypeArray['event_id'] = $eventId;
            
                // If ticket type exists and the name isnt set, it will be removed.
                if(isset($ticketTypeArray['ticket_type_id']) && $ticketTypeArray['name'] == '')
                {
                    // Delete ticket type 
                    $events->deleteTicketType($ticketTypeArray['ticket_type_id']);
                }
                else
                {
                    // save ticket type
                    $events->saveTicketType($ticketTypeArray);
                }
            endforeach;    
            // Redirect to admin/index
            $this->_redirect('/admin');
        }

        // Populate form with data.
        
        if(isset($post['submit']))
        {
            $vars = $post;
        }
        else
        {
        // Form step 1
        $step1 = array(
            'name' => $event->name,
            'location'  => $event->location,
            'details'   => $event->details,          
            'start_time'=> $event->start_time,
            'end_time'  => $event->end_time,
            );

        // Form step 2
        $step2 = array();
        foreach ($ticketTypes as $ticketType):
            //var_dump($ticketType);
            $data = array(
                'name'              => $ticketType->name,
                'quantity'          => $ticketType->quantity,
                'price'             => $ticketType->price,
                'details'           => $ticketType->details,
                'ticket_type_id'    => $ticketType->ticket_type_id,
                'order'             => $ticketType->order
            );
            $step2[] = $data;
        endforeach;
        //var_dump($step2);
        // Form step 3
        $step3 = array(
            'public' => $event->public
        );
        
        // Form steps
        $vars = array(
            'step1' => $step1,
            'step2' => $step2,
            'step3' => $step3
        );
        }
        // Populate form with data
        $form->populate($vars);
        
        // Set EventId when edit
        $form->setEventId($eventId);
        
        // Send form to view
        $this->view->form = $form;
        
    }
    
    public function deleteAction()
    {
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        
        // Initiate object
        $events = new Admin_Model_AdminEvents();

        // Filter eventId
        $filter = new Zend_Filter_Digits();
        $eventId = $filter->filter($_GET['event_id']);
        
        // Get event for flashMessenger
        $event = $events->getEvent($eventId);
        
        // Delete event
        $events->deleteEvent($eventId);
        
        // Set message
        $flashMessenger->addMessage($event->name. ' har tagits bort!');
                
        // Redirect to admin/index
        $this->_redirect('/admin');
    }
    
}

