<?php
class Admin_Form_SubForm_TicketType extends Generic_Form_SubForm_Base {

	const REMOVE_TICKET_TYPE_SUBMIT = 'remove_ticket_type';
	/**
	 * Instance of Zend_Translate
	 * @var Zend_Translate $_translator
	 */
	protected $_translator;

	/**
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

	/**
	 * Add form elements
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function init()
	{
		// Get default form translator
		$this->_translator = $this->getTranslator();

		// Add name
		$this->addElement('text', 'name', array(
			'label'		=> $this->_translator->translate('Ticket Name'),
			'required'	=> false,
			'class'		=> 'name',
			'filters'	=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty', new Admin_Validate_TicketTypeName()),
		));
		$name = $this->getElement('name');

		// Add quantity
		$this->addElement('text', 'quantity', array(
			'label'			=> $this->_translator->translate('Ticket Quantity'),
			'required'		=> false,
			'class'			=> 'quantity',
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty', new Admin_Validate_TicketTypeQuantity()),
		));

		// Add price
		$this->addElement('text', 'price', array(
			'label'		=> $this->_translator->translate('Ticket Price'),
			'required'		=> false,
			'class'			=> 'price',
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty', new Admin_Validate_TicketTypePrice()),
		));

		// Add details
		$this->addElement('textarea', 'details', array(
			'label'			=> $this->_translator->translate('Details'),
			'required'		=> false,
			'class'			=> 'details',
			'filters'		=> array('StringTrim','StripTags'),
			'rows'			=> '2',
			'cols'			=> '130',
		));

		// Add submit button
		$this->addElement('submit', self::REMOVE_TICKET_TYPE_SUBMIT, array(
			'label' => $this->_translator->translate('Remove Ticket Type'),
			'class' => 'remove_ticket_type'
		));

		// Add ticket type id
		$ticketTypeIdColNameForForm = Attend_Db_Table_Row_TicketType::getColumnNameForUrl('ticketTypeId', '_');
		$this->addElement('hidden',$ticketTypeIdColNameForForm, array(
			'class'			=> $ticketTypeIdColNameForForm));

		// Add hidden order num
		$this->addElement('hidden','order', array('class' => 'order'));
	}
}
