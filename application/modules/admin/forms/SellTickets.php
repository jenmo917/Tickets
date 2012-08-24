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
	 * This function adds all form elements, run create to set the valid ticket typs.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function init()
	{
		// Form element names
		$nameElementName			= Attend_Db_Table_Row_Ticket::getColumnNameForUrl('name', '_');
		$emailElementName			= Attend_Db_Table_Row_Ticket::getColumnNameForUrl('email', '_');
		$liuIdElementName			= Attend_Db_Table_Row_Ticket::getColumnNameForUrl('liuId', '_');
		$ticketTypeIdElementName	= Attend_Db_Table_Row_Ticket::getColumnNameForUrl('ticketTypeId', '_');
		$paymentElementName			= Attend_Db_Table_Row_Ticket::getColumnNameForUrl('payment', '_');

		// Get the default form translator
		$this->_translator = $this->getTranslator();

		// Add LiU-ID
		$this->addElement('text', $liuIdElementName, array(
			'label'			=> $this->_translator->translate('LiU-ID'),
			'required'		=> false,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('Alnum'),
			'errorMessages'	=> array($this->_translator->translate('Only digits and letters in LiU-ID please').'.'),
		));

		// Add Name
		$this->addElement('text', $nameElementName, array(
			'label'			=> $this->_translator->translate('Name'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty'),
			'errorMessages'	=> array($this->_translator->translate('Name please').'.'),
		));

		// Add Email
		$this->addElement('text', $emailElementName, array(
			'label'			=> $this->_translator->translate('Email'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('EmailAddress'),
			'errorMessages'	=> array($this->_translator->translate('Email please').'.'),
		));

		// Add Ticket Type
		$ticketType = new Admin_Form_Element_TicketTypeSelect($ticketTypeIdElementName, array(
			'label'			=> $this->_translator->translate('Ticket Type'),
			'decorators'	=> $this->elementDecorators,
			'errorMessages'	=> array($this->_translator->translate('Ticket Type please')),
			'required'		=> true,
		));
		$ticketType->setEventID($this->_eventID);
		//$ticketType->create();
		$this->addElement($ticketType);

		// Add Cash or Invoice
		$this->addElement('radio',$paymentElementName, array(
			'label'			=> $this->_translator->translate('Payment Options'),
			'required'		=> true,
			'errorMessages'	=> array($this->_translator->translate('Select payment type please')),
			'multiOptions'	=> array(
				"cash"		=> $this->_translator->translate('Cash'),
				"invoice"	=> $this->_translator->translate('Send Invoice')),
		));

		// Add submit button
		$this->addElement('submit', self::REGISTER_TICKET_SUBMIT, array(
			'label' => $this->_translator->translate('Register Ticket')));
	}
}
