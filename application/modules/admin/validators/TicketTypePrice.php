<?php
class Admin_Validate_TicketTypePrice extends Zend_Validate_Abstract
{
	protected $_translator;

	/**
	 * Error codes
	 * @const string
	 */
	const ERROR = 'error';

	/**
	 * Error messages
	 * @var array
	 */
	protected $_messageTemplates;

	public function isValid($value, $context = null)
	{
		$this->_translator = $this->getTranslator();

		$this->_messageTemplates = array(
		self::ERROR => $this->_translator->translate('Please enter a number').'.',
		);

		$unsetArr = array(
			'ticketTypeIdElementName'	=> Attend_Db_Table_Row_TicketType::getColumnNameForUrl('ticketTypeId', '_'),
			'priceElementName'		=> Attend_Db_Table_Row_TicketType::getColumnNameForUrl('price', '_'),
			'orderElementName'			=> Attend_Db_Table_Row_TicketType::getColumnNameForUrl('order', '_'),);
		foreach ($unsetArr as $unset)
		{
			if (isset($context[$unset]))
			unset($context[$unset]);
		}
		if(isset($value) && $value != '' && !is_numeric($value))
		{
			$this->_error(self::ERROR);
			return false;
		}

		// If any other required field is empty return false
		foreach ($context as $data):
		if(isset($data) && $data != '' && !is_numeric($value))
		{
			$this->_error(self::ERROR);
			return false;
		}
		endforeach;

		return true;
	}
}
