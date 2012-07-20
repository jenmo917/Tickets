<?php
class Admin_Form_SubForm_TicketType extends Zend_Form_SubForm {

    /**
    * Instance of Zend_Translate
    * @var Zend_Translate $_translator
    */    
    protected $_translator;
    
    /*
    * Set event ID
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */ 
    public function setEventId($eventId)
    {
        $hidden = $this->createElement('hidden', 'event_id');
        $hidden->setValue($eventId);
        $this->addElement($hidden);
    }

    /*
    * Add form elements
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */ 
	public function init()
    {
        // Get default form translator
        $this->_translator = $this->getTranslator();
        
        // Set form decorators
        $this->setDecorators(array(
            'FormElements',
            array('fieldset', array('class' => 'ticket_type'))
        ));
                
        // Add name        
        $name  = $this->createElement('text', 'name', array(
            'label'      => $this->_translator->translate('Ticket Name'),
            'required'   => false,
            'class'      => 'name',
            'filters'    => array('StringTrim','StripTags'),
        ));
        $name->setAllowEmpty(false);
        $name->addValidator(new Admin_Validate_TicketTypeName());
        $this->addElement($name);

        // Add quantity
        $quantity  = $this->createElement('text', 'quantity', array(
            'label'      => $this->_translator->translate('Ticket Quantity'),
            'required'   => false,
            'class'      => 'quantity',            
            'filters'    => array('StringTrim','StripTags')
        ));
        $quantity->addValidator(new Admin_Validate_TicketTypeQuantity());
        $quantity->setAllowEmpty(false);
        
        $this->addElement($quantity);
        
        // Add price
        $price  = $this->createElement('text', 'price', array(
            'label'      => $this->_translator->translate('Ticket Price'),
            'required'   => false,
            'class'      => 'price',            
            'filters'    => array('StringTrim','StripTags')
        ));      
        $price->addValidator(new Admin_Validate_TicketTypePrice());
        $price->setAllowEmpty(false);
      
        $this->addElement($price); 
    
        // Add details
        $details = $this->createElement('textarea', 'details', array(
            'label'      => $this->_translator->translate('Details'),
            'required'   => false,
            'class'      => 'details',            
            'filters'    => array('StringTrim','StripTags')
        ));
        $details->setAttrib('COLS', '130')
                ->setAttrib('ROWS', '2');
        $this->addElement($details);

        // Add submit button
        $submit = $this->createElement('submit', 'submit', array(
            'label' => $this->_translator->translate('Remove Ticket Type'),
            'class' => 'remove_ticket_type'
                ));
        $this->addElement($submit);
        
        // Add ticket type id
        $ticketTypeId = $this->createElement('hidden','ticket_type_id',array(
            'class' => 'ticket_type_id'
        ));
        
        // Set decorators
        $ticketTypeId->setDecorators(array('ViewHelper'));
        $this->addElement($ticketTypeId);
        
        // Add hidden order num
        $order = $this->createElement('hidden','order',array(
            'class' => 'order'
        ));
        
        // Set decorators
        $order->setDecorators(array('ViewHelper'));
        $this->addElement($order);
                
    }
}