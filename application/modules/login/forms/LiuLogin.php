<?php

class Login_Form_LiuLogin extends Generic_Form_Base
{
	const LIU_SIGN_IN_SUBMIT = 'liu_sign_in';
	
	public function init()
	{
		$method = 'post';
		$this->setMethod($method);
		$this->setAttrib('id', 'login-confirm')->setAttrib('filter', array('StripTags', 'StringTrim'));

		$this->addElement('submit', self::LIU_SIGN_IN_SUBMIT, array(	'label' => gettext('Sign in'),
																		'decorators' => $this::$buttonDecorators
		));
	}
}