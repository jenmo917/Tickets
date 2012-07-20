<?php

class Admin_Form_SellTickets extends Zend_Form
{
    /**
    * Instance of Zend_Translate
    * @var Zend_Translate $_translator
    */    
    protected $_translator;
    /**
    * Event ID
    * @var int
    */      
    private $_eventID;

    /*
    * Set event ID
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */    
    public function setEventID($eventId)
    {
        $this->_eventID = $eventId;
    }
    
    /*
    * This function add all form elements
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */     
    public function create()
    {
        // get the default form translator
        $this->_translator = $this->getTranslator();

        // Add LiU-ID
        $liuID = $this->createElement('text', 'liuid', array(
            'label'      => $this->_translator->translate('LiU-ID'),
            'required'   => false,
            'filters'    => array('StringTrim','StripTags'),
            'validators' => array('Alnum')
        ));
        $liuID->addErrorMessage($this->_translator->translate('Only digits and letters in LiU-ID please').'.');
        $this->addElement($liuID);        
        
        // Add Name
        $name = $this->createElement('text', 'name', array(
            'label'      => $this->_translator->translate('Name'),
            'required'   => true,
            'filters'    => array('StringTrim','StripTags'),
            'validators' => array('notEmpty')
        ));     
        $name->addErrorMessage($this->_translator->translate('Name please').'.');
        $this->addElement($name);
        
        // Add Email
        $email = $this->createElement('text', 'email', array(
            'label'      => $this->_translator->translate('Email'),
            'required'   => true,
            'filters'    => array('StringTrim','StripTags'),
            'validators' => array('EmailAddress')
        ));     
        $email->addErrorMessage($this->_translator->translate('Email please').'.');
        $this->addElement($email);

        // Add Ticket Type
        $ticketType = new Admin_Form_Element_TicketTypeSelect('ticket_type_id');
        $ticketType->setEventID($this->_eventID);
        $ticketType->create();
        $ticketType->setRequired(true)
                   ->addErrorMessage($this->_translator->translate('Ticket Type please'))
                   ->setOptions(array(
        	'label' => $this->_translator->translate('Ticket Type')
        ));
        $this->addElement($ticketType);
        
        // Add Cash or Invoice
        $payment = $this->createElement('radio','payment');
        $payment->addMultiOption("cash","Cash")
                ->addMultiOption("invoice","Send Invoice")
                ->setRequired(true)
                ->setLabel("Payment Options")
                ->addErrorMessage($this->_translator->translate('Select payment type please'));
        $this->addElement($payment);
        
        // Add submit button
        $this->addElement('submit', 'submit', array('label' => $this->_translator->translate('Register Ticket Payment')));
        
    }
}