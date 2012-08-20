<?php

class Admin_Form_SellTickets extends Generic_Form_Base
{
	const REGISTER_TICKET_SUBMIT = 'register_ticket';
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

	/**
	 * Set event ID
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function setEventID($eventId)
	{
		$this->_eventID = $eventId;
	}

	/**
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
			'label'			=> $this->_translator->translate('LiU-ID'),
			'required'		=> false,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('Alnum'),
			'errorMessages'	=> $this->_translator->translate('Only digits and letters in LiU-ID please').'.',
		));

		// Add Name
		$this->addElement('text', 'name', array(
			'label'			=> $this->_translator->translate('Name'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty'),
			'errorMessages'	=> $this->_translator->translate('Name please').'.',
		));

		// Add Email
		$this->addElement('text', 'email', array(
			'label'			=> $this->_translator->translate('Email'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('EmailAddress'),
			'errorMessages'	=> $this->_translator->translate('Email please').'.',
		));

		// Add Ticket Type
		$ticketTypeIdColName = Admin_Model_DbTable_Row_TicketType::getColumnNameForUrl('ticketTypeId', '_');
		$ticketType = new Admin_Form_Element_TicketTypeSelect($ticketTypeIdColName, array(
			'label' => $this->_translator->translate('Ticket Type'),
			'decorators' => $this->elementDecorators,
			'errorMessages'	=> $this->_translator->translate('Ticket Type please'),
		));
		$ticketType->setEventID($this->_eventID);
		$ticketType->create();
		$ticketType->setRequired(true);
		$this->addElement($ticketType);

		// Add Cash or Invoice
		$this->addElement('radio','payment', array(
			'label'			=> $this->_translator->translate('Payment Options'),
			'required'		=> true,
			'errorMessages'	=> $this->_translator->translate('Select payment type please'),
			'multiOptions'	=> array(
				"cash"		=> $this->_translator->translate('Cash'),
				"invoice"	=> $this->_translator->translate('Send Invoice')),
		));

		// Add submit button
		$this->addElement('submit', self::REGISTER_TICKET_SUBMIT, array(
			'label' => $this->_translator->translate('Register Ticket')));
	}
}
