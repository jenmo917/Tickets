<?php
class Admin_Form_SubForm_TicketType extends Generic_Form_SubForm_Base {

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
        /*$this->setDecorators(array(
            'FormElements',
            array('fieldset', array('class' => 'ticket_type'))
        ));*/

        // Add name
        $this->addElement('text', 'name', array(
            'label'      => $this->_translator->translate('Ticket Name'),
            'required'   => false,
            'class'      => 'name',
            'filters'    => array('StringTrim','StripTags'),
        ));
        $name = $this->getElement('name');
        $name->setAllowEmpty(false);
        $name->addValidator(new Admin_Validate_TicketTypeName());

        // Add quantity
        $this->addElement('text', 'quantity', array(
            'label'      => $this->_translator->translate('Ticket Quantity'),
            'required'   => false,
            'class'      => 'quantity',
            'filters'    => array('StringTrim','StripTags')
        ));
        $quantity  = $this->getElement('quantity');
        $quantity->addValidator(new Admin_Validate_TicketTypeQuantity());
        $quantity->setAllowEmpty(false);

        // Add price
        $this->addElement('text', 'price', array(
            'label'      => $this->_translator->translate('Ticket Price'),
            'required'   => false,
            'class'      => 'price',
            'filters'    => array('StringTrim','StripTags')
        ));
        $price = $this->getElement('price');
        $price->addValidator(new Admin_Validate_TicketTypePrice());
        $price->setAllowEmpty(false);

        // Add details
        $this->addElement('textarea', 'details', array(
            'label'      => $this->_translator->translate('Details'),
            'required'   => false,
            'class'      => 'details',
            'filters'    => array('StringTrim','StripTags')
        ));
        $details = $this->getElement('details');
        $details->setAttrib('COLS', '130')
                ->setAttrib('ROWS', '2');

        // Add submit button
        $this->addElement('submit', 'removeTicketType', array(
            'label' => $this->_translator->translate('Remove Ticket Type'),
            'class' => 'remove_ticket_type'
                ));

        // Add ticket type id
        $ticketTypeIdColNameForUrl = Admin_Model_DbTable_Row_TicketType::getColumnNameForUrl('ticketTypeId');
        $this->addElement('hidden',$ticketTypeIdColNameForUrl,
        					array('class' => $ticketTypeIdColNameForUrl));

        // Set decorators
        $ticketTypeId = $this->getElement($ticketTypeIdColNameForUrl);
        $ticketTypeId->setDecorators(array('ViewHelper'));

        // Add hidden order num
        $this->addElement('hidden','order',
        					array('class' => 'order'));
        $order = $this->getElement('order');
        // Set decorators
        $order->setDecorators(array('ViewHelper'));
    }
}
