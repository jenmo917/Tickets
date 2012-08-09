<?php

class Login_Form_Confirm extends Zend_Form
{
	public function init()
	{
		$method = 'get';
		$this->setDecorators(array( 'FormElements',
									array	(	'HtmlTag',
												array('tag' => 'div', 'class' => 'form')
											),
									'Form')
							);

		$this->setMethod($method);
		$this->setAttrib('id', 'login-confirm')->setAttrib('filter', array('StripTags', 'StringTrim'));

		$this->addElement('submit', 'ok', array(	'label' => 'Alrigt, I approve', 'class' => 'main-button'	));
		$this->addElement('submit', 'cancel', array(	'label' => 'Cancel', 'class' => 'cancel-button'	));
	}
}