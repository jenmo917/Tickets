<?php
class Admin_Form_SubForm_TicketType extends Generic_Form_SubForm_Base {

	const REMOVE_TICKET_TYPE_SUBMIT = 'remove_ticket_type';

	/**
	 * Set event ID
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function setEventId($eventId)
	{
		$nameElementName	= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('name', '_');
		$eventIdElement		= $this->getElement('hidden', $nameElementName);
		$eventIdElement->setValue($eventId);
	}

	/**
	 * Add form elements
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function init()
	{
		// Form element names
		$ticketTypeIdElementName	= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('ticketTypeId', '_');
		$eventIdElementName			= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('eventId', '_');
		$nameElementName			= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('name', '_');
		$priceElementName			= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('price', '_');
		$quantityElementName		= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('quantity', '_');
		$detailsElementName			= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('details', '_');
		$orderElementName			= Attend_Db_Table_Row_TicketType::getColumnNameForUrl('order', '_');

		// Add name
		$this->addElement('text', $nameElementName, array(
			'label'			=> gettext('Ticket Name'),
			'required'		=> false,
			'class'			=> 'name',
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty', new Admin_Validate_TicketTypeName()),
		));

		// Add quantity
		$this->addElement('text', $quantityElementName, array(
			'label'			=> gettext('Ticket Quantity'),
			'required'		=> false,
			'class'			=> 'quantity',
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty', new Admin_Validate_TicketTypeQuantity()),
		));

		// Add price
		$this->addElement('text', $priceElementName, array(
			'label'		=> gettext('Ticket Price'),
			'required'		=> false,
			'class'			=> 'price',
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty', new Admin_Validate_TicketTypePrice()),
		));

		// Add details
		$this->addElement('textarea', $detailsElementName, array(
			'label'			=> gettext('Details'),
			'required'		=> false,
			'class'			=> 'details',
			'filters'		=> array('StringTrim','StripTags'),
			'rows'			=> '6',
			'cols'			=> '50',
		));

		// Add submit button
		$this->addElement('submit', self::REMOVE_TICKET_TYPE_SUBMIT, array(
			'label' => gettext('Remove Ticket Type'),
			'class' => 'remove_ticket_type',
			'disabled'		=> true,
			'decorators' => $this::$buttonDecorators
		));

		// Add ticket type id
		$this->addElement('hidden',$ticketTypeIdElementName, array(
			'class'			=> 'ticket_type_id'));

		// Add event id
		$this->addElement('hidden',$eventIdElementName, array(
			'class'			=> 'ticket_type_id'));

		// Add hidden order num
		$this->addElement('hidden',$orderElementName, array('class' => 'order'));
	}

	public function removeButtonDisabled($bool)
	{
		if (is_bool($bool))
		{
			if (true === $bool)
				$this->getElement(self::REMOVE_TICKET_TYPE_SUBMIT)->setAttrib('disabled', $bool);
			else
				$this->getElement(self::REMOVE_TICKET_TYPE_SUBMIT)->setAttrib('disabled', null);
		}
	}
}
