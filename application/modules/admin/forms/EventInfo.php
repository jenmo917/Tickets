<?php

class Admin_Form_EventInfo extends Zend_Form
{   
    protected $_translator;
    
    public function setEventId($eventId)
    {
        $hidden = $this->createElement('hidden', 'event_id');
        $hidden->setValue($eventId);
        $this->addElement($hidden);
    }

    public function init()
    {
        $this->_translator = $this->getTranslator();
    }
    
    public function create($numOfTicketTypes)
    {
        /*
         *  STEP 1
         */
        $step1 = new Zend_Form_SubForm();
        
        $email = $this->createElement('text', 'name', array(
            'label'      => $this->_translator->translate('Name'),
            'required'   => true,
            'filters'    => array('StringTrim','StripTags'),
            'validators' => array('notEmpty')
        ));     
        $email->addErrorMessage($this->_translator->translate('Name please').'.');
        $step1->addElement($email);

        $location = $this->createElement('text', 'location', array(
            'label'      => $this->_translator->translate('Location'),
            'required'   => true,
            'filters'    => array('StringTrim','StripTags'),
            'validators' => array('notEmpty')
        ));
        $location->addErrorMessage($this->_translator->translate('Location please').'.');
        $step1->addElement($location);
        
        $startTime = $this->createElement('text', 'start_time', array(
            'label'      => $this->_translator->translate('Event starts'),
            'required'   => true,
            'filters'    => array('StringTrim','StripTags'),
        ));
        $startTime->class = 'date-pick';
        $startTime->addErrorMessage($this->_translator->translate('Start time please').'.');
        $step1->addElement($startTime);
        
        $endTime = $this->createElement('text', 'end_time', array(
            'label'      => $this->_translator->translate('Event ends'),
            'required'   => true,
            'filters'    => array('StringTrim','StripTags')
        ));
        $endTime->class = 'date-pick';
        $endTime->addErrorMessage($this->_translator->translate('End time please').'.');
        $step1->addElement($endTime);
        
        $details = $this->createElement('textarea', 'details', array(
            'label'      => $this->_translator->translate('Details'),
            'required'   => false,
            'filters'    => array('StringTrim','StripTags')
        ));
        $details->setAttrib('COLS', '130')
                ->setAttrib('ROWS', '2');
        $step1->addElement($details);        

        /*
         *  STEP 2
         */
        $step2 = new Zend_Form_SubForm();
        $i = 0;
        while($i < $numOfTicketTypes)
        {
            $ticketType = new Admin_Form_SubForm_TicketType();
            $ticketType->setDefault('order',$i);
            $step2->addSubForm($ticketType, $i);
            $i++;
        }
       
        
        $step2->addElement('submit', 'submit', array('label' => $this->_translator->translate('New Ticket Type')));
        /*
         *  STEP 3
         */
        $step3 = new Zend_Form_SubForm();

        $public = new Zend_Form_Element_Select("public");

        $public ->setLabel($this->_translator->translate('Publicize, or keep it private'))
        ->addMultiOptions(array(
            "1" => $this->_translator->translate('Public'),
            "0" => $this->_translator->translate('Private')
        ));
        $step3->addElement($public);
        
        /*
         *  Overall actions
         */
        
        // Set legends
        $step1->setLegend($this->_translator->translate('Step 1 - Add your Event Details'));
        $step2->setLegend($this->_translator->translate('Step 2 - Create Tickets'));
        $step3->setLegend($this->_translator->translate('Step 3 - Promote your Event Page'));

        // Attach sub forms to main form
        $this->addSubForms(array(
            'step1'  => $step1,
            'step2'  => $step2,
            'step3'  => $step3
        ));
        
        $this->addElement('submit', 'submit', array('label' => $this->_translator->translate('Save Event')));
    }
}