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
	 * This function add all form elements
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function create()
	{
		$this->_translator = $this->getTranslator();
		$ticketTypeIdColName = Admin_Model_DbTable_Row_TicketType::getColumnName('ticketTypeId');
		$this->addMultiOption('', $this->_translator->translate('Select Ticket Type'));
		$model = new Admin_Model_AdminEvents();

		$result = $model->getTicketTypes($this->_eventID);

		foreach ($result as $ticketType) {
			if($ticketType['quantity'] > $ticketType['sold_tickets'])
			{
				$num = $ticketType['quantity']-$ticketType['sold_tickets'];
				$this->addMultiOption($ticketType[$ticketTypeIdColName], $ticketType['name'].' - '.$ticketType['price'].'kr - '.$num.$this->_translator->translate(' tickets left'));
			}
		}
	}
}
