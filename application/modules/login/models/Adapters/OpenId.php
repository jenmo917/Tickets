<?php
class Login_Model_Adapter_OpenId implements Zend_Auth_Adapter_Interface
{
	protected $_identifier;
	
	public function __construct($options)
	{
	}
	
	public function authenticate()
	{
		if (!empty($this->_identifier))
		{
			$consumer = new Zend_OpenId_Consumer();
			if (!$consumer->login($this->_identifier))//, $this->getLoginUrl()
			{
				$ret = false;
				$msg = "Authentication failed.";
			}
		}
		else
		{
			$consumer = new Zend_OpenId_Consumer();
			if ($consumer->verify($_GET, $this->_identifier)) {
				$ret = true;
				$msg = "Authentication successful";
			} else {
				$ret = false;
				$msg = "Authentication failed";
			}
		}
		return new Zend_Auth_Result($ret, $this->_identifier, array($msg));
	}
	
	public function setIdentifier($identifier)
	{
		$this->_identifier = $identifier;
	}
}