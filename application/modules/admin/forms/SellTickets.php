<?php

class Admin_Form_SellTickets extends Generic_Form_Base
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
        $this->addElement('text', 'liuid', array(
            'label'      => $this->_translator->translate('LiU-ID'),
            'required'   => false,
            'filters'    => array('StringTrim','StripTags'),
            'validators' => array('Alnum')
        ));
        $liuID = $this->getElement('liuid');
        $liuID->addErrorMessage($this->_translator->translate('Only digits and letters in LiU-ID please').'.');
        //$this->addElement($liuID);

        // Add Name
        $this->addElement('text', 'name', array(
            'label'      => $this->_translator->translate('Name'),
            'required'   => true,
            'filters'    => array('StringTrim','StripTags'),
            'validators' => array('notEmpty')
        ));
        $name = $this->getElement('name');
        $name->addErrorMessage($this->_translator->translate('Name please').'.');

        // Add Email
        $this->addElement('text', 'email', array(
            'label'      => $this->_translator->translate('Email'),
            'required'   => true,
            'filters'    => array('StringTrim','StripTags'),
            'validators' => array('EmailAddress')
        ));
        $email = $this->getElement('email');
        $email->addErrorMessage($this->_translator->translate('Email please').'.');

        // Add Ticket Type
        $ticketTypeIdColName = Admin_Model_DbTable_Row_TicketType::getColumnNameForUrl('ticketTypeId', '_');
        $ticketType =
	        new Admin_Form_Element_TicketTypeSelect(
	        		$ticketTypeIdColName,
	        		array(	'label' => $this->_translator->translate('Ticket Type'),
	        				'decorators' => $this->elementDecorators,
	        		)
	        );
        $ticketType->setEventID($this->_eventID);
        $ticketType->create();
        $ticketType->setRequired(true)
                   ->addErrorMessage($this->_translator->translate('Ticket Type please'));
        $this->addElement($ticketType);

        // Add Cash or Invoice
        $this->addElement('radio','payment');
        $payment = $this->getElement('payment');
        $payment->addMultiOption("cash",$this->_translator->translate('Cash'))
                ->addMultiOption("invoice",$this->_translator->translate('Send Invoice'))
                ->setRequired(true)
                ->setLabel($this->_translator->translate('Payment Options'))
                ->addErrorMessage($this->_translator->translate('Select payment type please'));

        // Add submit button
        $this->addElement('submit', 'submit', array('label' => $this->_translator->translate('Register Ticket')));

    }
}