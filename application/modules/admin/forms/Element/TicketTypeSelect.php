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

	/**
	 * Initiates ticket types in the multioption choise.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array $ticketTypes
	 */
	public function create(array $ticketTypes)
	{
		$ticketTypeForm = Attend_Db_Table_Row_TicketType::getColumnNames('both', '_');
		foreach ($result as $ticketType)
		{
			if($ticketType[$ticketTypeForm['quantity']] >
				$ticketType[$ticketTypeForm['soldTickets']])
			{
				$num = $ticketType[$ticketTypeForm['quantity']] -
						$ticketType[$ticketTypeForm['soldTickets']];

				$text  =	$ticketType[$ticketTypeForm['name']].' - ';
				$text .=	$ticketType[$ticketTypeForm['price']].'kr - ';
				$text .=	$num.$this->_translator->translate(' tickets left');

				$this->addMultiOption($ticketType[$ticketTypeForm['name']], $text);
			}
		}
	}
}
