<?php
class Admin_Form_SubForm_TicketType extends Zend_Form_SubForm {
    
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
        
        $this->setDecorators(array(
            'FormElements',
            array('fieldset', array('class' => 'ticket_type'))
        ));
                
                
        $name  = $this->createElement('text', 'name', array(
            'label'      => $this->_translator->translate('Ticket Name'),
            'required'   => false,
            'class'      => 'name',
            'filters'    => array('StringTrim','StripTags'),
        ));
        $name->setAllowEmpty(false);
        $name->addValidator(new Admin_Validate_TicketTypeName());
        $this->addElement($name);

        $quantity  = $this->createElement('text', 'quantity', array(
            'label'      => $this->_translator->translate('Ticket Quantity'),
            'required'   => false,
            'class'      => 'quantity',            
            'filters'    => array('StringTrim','StripTags')
        ));
        $quantity->addValidator(new Admin_Validate_TicketTypeQuantity());
        $quantity->setAllowEmpty(false);
        
        $this->addElement($quantity);
        $price  = $this->createElement('text', 'price', array(
            'label'      => $this->_translator->translate('Ticket Price'),
            'required'   => false,
            'class'      => 'price',            
            'filters'    => array('StringTrim','StripTags')
        ));      
        $price->addValidator(new Admin_Validate_TicketTypePrice());
        $price->setAllowEmpty(false);
      
        $this->addElement($price); 
    
        $details = $this->createElement('textarea', 'details', array(
            'label'      => $this->_translator->translate('Details'),
            'required'   => false,
            'class'      => 'details',            
            'filters'    => array('StringTrim','StripTags')
        ));
        $details->setAttrib('COLS', '130')
                ->setAttrib('ROWS', '2');
        $this->addElement($details);

        $submit = $this->createElement('submit', 'submit', array(
            'label' => $this->_translator->translate('Remove Ticket Type'),
            'class' => 'remove_ticket_type'
                ));
        $this->addElement($submit);
        
        $ticketTypeId = $this->createElement('hidden','ticket_type_id',array(
            'class' => 'ticket_type_id'
        ));
        
        $ticketTypeId->setDecorators(array('ViewHelper'));
        $this->addElement($ticketTypeId);
        
        $order = $this->createElement('hidden','order',array(
            'class' => 'order'
        ));
        $order->setDecorators(array('ViewHelper'));
        $this->addElement($order);
                
    }
}