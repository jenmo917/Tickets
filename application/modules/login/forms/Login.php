<?php
class Login_Form_Login extends Zend_Form
{
	private $_timeout;

	public function __construct($options=null) {
		if (is_array($options)) {
			if (!empty($options['custom'])) {
				if (!empty($options['custom']['timeout'])) {
					$this->_timeout= $options['custom']['timeout'];
				}
				unset($options['custom']);
			}
		}
		parent::__construct($options);
	}

	public function init ()
	{
		$this->addElement('hash', 'token', array(
				'timeout' => $this->_timeout
		));

		$this->addElement('text', 'username', array(
				'label'			=> gettext('LiU-ID').': ',
				'required'		=> true,
				'filters'		=> array('StringTrim','StripTags'),
				'validators'	=> array('Alnum')
		));
		$this->addElement('password', 'password', array(
				'label'			=> gettext('Password').': ',
				'required'		=> true,
				'filters'		=> array('StringTrim','StripTags'),
				'validators'	=> array('Alnum'),
		));
		$this->addElement('submit','submit', array (
				'label'			=> gettext('Log in'),
				'decorators'	=> $this::$buttonDecorators,
		));
	}
}