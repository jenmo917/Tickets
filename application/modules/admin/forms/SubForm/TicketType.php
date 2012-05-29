<?php
class Admin_Form_SubForm_TicketType extends Zend_Form_SubForm {
    
	public function init()
    {
        $this->setDecorators(array(
            'FormElements',
            array('fieldset', array('class' => 'ticket_type'))
        ));
                
                
        $name  = $this->createElement('text', 'name', array(
            'label'      => 'Ticket Name: ',
            'required'   => false,
            'class'      => 'name',
            'filters'    => array('StringTrim','StripTags'),
        ));
        $name->setAllowEmpty(false);
        $name->addValidator(new Admin_Validate_TicketTypeName());
        $this->addElement($name);

        $quantity  = $this->createElement('text', 'quantity', array(
            'label'      => 'Ticket Quantity: ',
            'required'   => false,
            'class'      => 'quantity',            
            'filters'    => array('StringTrim','StripTags')
        ));
        $quantity->addValidator(new Admin_Validate_TicketTypeQuantity());
        $quantity->setAllowEmpty(false);
        
        $this->addElement($quantity);
        $price  = $this->createElement('text', 'price', array(
            'label'      => 'Ticket Price: ',
            'required'   => false,
            'class'      => 'price',            
            'filters'    => array('StringTrim','StripTags')
        ));      
        $price->addValidator(new Admin_Validate_TicketTypePrice());
        $price->setAllowEmpty(false);
      
        $this->addElement($price); 
    
        $details = $this->createElement('textarea', 'details', array(
            'label'      => 'Details: ',
            'required'   => false,
            'class'      => 'details',            
            'filters'    => array('StringTrim','StripTags')
        ));
        $details->setAttrib('COLS', '130')
                ->setAttrib('ROWS', '2');
        $this->addElement($details);
        
        $this->addElement('hidden','ticket_type_id');
        
        $order = $this->createElement('hidden','order',array(
            'class' => 'order'
        ));
        $this->addElement($order);
    }
}