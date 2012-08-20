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

		unset($context['price']);
		unset($context['order']);
		unset($context['ticket_type_id']);

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
