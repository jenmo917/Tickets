<?php
class Admin_Validate_TicketTypeName extends Zend_Validate_Abstract
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
		self::ERROR => $this->_translator->translate('Please provide a ticket name').'.',
		);

		$unsetArr = array(
			'ticketTypeIdElementName'	=> Attend_Db_Table_Row_TicketType::getColumnNameForUrl('ticketTypeId', '_'),
			'nameElementName'			=> Attend_Db_Table_Row_TicketType::getColumnNameForUrl('name', '_'),
			'orderElementName'			=> Attend_Db_Table_Row_TicketType::getColumnNameForUrl('order', '_'),);
		foreach ($unsetArr as $unset)
		{
			if (isset($context[$unset]))
			unset($context[$unset]);
		}

		foreach ($context as $data):
		if(($value == '' || !isset($value)) && isset($data) && $data != '')
		{
			$this->_error(self::ERROR);
			return false;
		}
		endforeach;

		return true;
	}
}
