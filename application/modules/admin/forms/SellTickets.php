<?php

class Admin_Form_SellTickets extends Generic_Form_Base
{
	const REGISTER_TICKET_SUBMIT = 'register_ticket';
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

		// Add LiU-ID
		$this->addElement('text', $liuIdElementName, array(
			'label'			=> gettext('LiU-ID'),
			'required'		=> false,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('Alnum'),
			'errorMessages'	=> array(gettext('Only digits and letters in LiU-ID please')),
		));

		// Add Name
		$this->addElement('text', $nameElementName, array(
			'label'			=> gettext('Name'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty'),
			'errorMessages'	=> array(gettext('Name please')),
		));

		// Add Email
		$this->addElement('text', $emailElementName, array(
			'label'			=> gettext('Email'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('EmailAddress'),
			'errorMessages'	=> array(gettext('Email please')),
		));

		// Add Ticket Type
		$ticketType = new Admin_Form_Element_TicketTypeSelect($ticketTypeIdElementName, array(
			'label'			=> gettext('Ticket Type'),
			'decorators'	=> $this->elementDecorators,
			'errorMessages'	=> array(gettext('Ticket Type please')),
			'required'		=> true,
		));
		$ticketType->setEventID($this->_eventID);
		//$ticketType->create();
		$this->addElement($ticketType);

		// Add Cash or Invoice
		$this->addElement('radio',$paymentElementName, array(
			'label'			=> gettext('Payment Options'),
			'required'		=> true,
			'errorMessages'	=> array(gettext('Select payment type please')),
			'multiOptions'	=> array(
				"cash"		=> gettext('Cash'),
				"invoice"	=> gettext('Send Invoice')),
			
		));

		// Add submit button
		$this->addElement('submit', self::REGISTER_TICKET_SUBMIT, array(
				'label' => gettext('Register Ticket')));
	}

	/**
	 * Initiates ticket types in the multioption choise by transferring
	 * the ticket type data to the specific element.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array $ticketTypes
	 */
	public function create(array $ticketTypes)
	{
		$ticketTypeIdElementName	= Attend_Db_Table_Row_Ticket::getColumnNameForUrl('ticketTypeId', '_');
		$this->getElement($ticketTypeIdElementName)->create($ticketTypes);
	}
}
