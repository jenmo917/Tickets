<?php

class Login_Form_Confirm extends Generic_Form_Base
{
	public function init()
	{
		$method = 'get';
		$this->setMethod($method);
		$this->setAttrib('id', 'login-confirm')->setAttrib('filter', array('StripTags', 'StringTrim'));

		$this->addElement('submit', 'ok', array(
				'label' => gettext('Alrigt, I approve'),
				'class' => 'main-button',
				'decorators' => $this::$buttonDecorators
				));
		$this->addElement('submit', 'cancel', array(
				'label' => gettext('Cancel'),
				'class' => 'cancel-button',
				'decorators' => $this::$buttonDecorators
				));
	}
}