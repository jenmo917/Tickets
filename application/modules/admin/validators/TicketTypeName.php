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

		unset($context['name']);
		unset($context['order']);
		unset($context['ticket_type_id']);

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
