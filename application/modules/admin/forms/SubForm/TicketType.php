<?php
class Admin_Form_SubForm_TicketType extends Zend_Form_SubForm {
    
	public function init()
    {         
        $name  = $this->createElement('text', 'name', array(
            'label'      => 'Ticket Name: ',
            'required'   => false,
            'filters'    => array('StringTrim','StripTags')
        ));
        $name->addValidator(new Admin_Validate_TicketType());
        $this->addElement($name);

        $name  = $this->createElement('text', 'name', array(
            'label'      => 'Ticket Name: ',
            'required'   => false,
            'filters'    => array('StringTrim','StripTags')
        ));
        $this->addElement($name);
        
        $quantity  = $this->createElement('text', 'quantity', array(
            'label'      => 'Ticket Quantity: ',
            'required'   => false,
            'filters'    => array('StringTrim','StripTags')
        ));
        $this->addElement($quantity);
        $price  = $this->createElement('text', 'price', array(
            'label'      => 'Ticket Price: ',
            'required'   => false,
            'filters'    => array('StringTrim','StripTags')
        ));      
        $this->addElement($price); 
    
        $details = $this->createElement('textarea', 'details', array(
            'label'      => 'Details: ',
            'required'   => false,
            'filters'    => array('StringTrim','StripTags')
        ));
        $details->setAttrib('COLS', '130')
                ->setAttrib('ROWS', '2');
        $this->addElement($details);
        
        $this->addElement('hidden','ticket_type_id');
        $this->addElement('hidden','order');        
    }
}