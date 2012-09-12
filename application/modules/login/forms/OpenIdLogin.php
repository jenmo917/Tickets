<?php

class Login_Form_OpenIdLogin extends Generic_Form_Base implements Login_Form_Interface
{
	const OPEN_ID_SIGN_IN_SUBMIT = 'open_id_sign_in';
	const OPEN_ID_IDENTIFICATION = 'open_id';
// 	protected static $identifications = array('open-id');
	
	public function init()
	{
		$method = 'post';
		$this->setMethod($method);
		$this->setAttrib('id', 'login-confirm')->setAttrib('filter', array('StripTags', 'StringTrim'));
		$this->addElement('text', self::OPEN_ID_IDENTIFICATION, array(
				'label'			=> gettext('openId').': ',
				'required'		=> true,
				'filters'		=> array('StringTrim','StripTags'),
				'validators'	=> array('Alnum'),
				));
		$this->addElement('submit', self::OPEN_ID_SIGN_IN_SUBMIT, array(	'label' => gettext('Sign in'),
				'decorators' => $this::$buttonDecorators
		));
	}
	
	public static function getIdentifierName()
	{
		return self::OPEN_ID_IDENTIFICATION;
	}
	
	public static function getSignInButtonName()
	{
		return self::OPEN_ID_SIGN_IN_SUBMIT;
	}
}