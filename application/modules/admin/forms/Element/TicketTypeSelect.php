<?PHP
class Admin_Form_Element_TicketTypeSelect extends Zend_Form_Element_Select {
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
	 * @return	int
	 */
	public function setEventID($eventId)
	{
		$this->_eventID = $eventId;
	}

	/**
	 * This function adds the element skeleton. Run create to set valid ticket types.
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function init()
	{
		$this->_translator = $this->getTranslator();
		$this->addMultiOption('', $this->_translator->translate('Select Ticket Type'));
	}

	public function create($ticketTypes)
	{
		$ticketTypeIdColName = Attend_Db_Table_Row_TicketType::getColumnNames('both', '_');
		foreach ($result as $ticketType)
		{
			if($ticketType['quantity'] > $ticketType['sold_tickets'])
			{
				$num = $ticketType['quantity']-$ticketType['sold_tickets'];
				$this->addMultiOption($ticketType[$ticketTypeIdColName], $ticketType['name'].' - '.$ticketType['price'].'kr - '.$num.$this->_translator->translate(' tickets left'));
			}
		}
	}
}
