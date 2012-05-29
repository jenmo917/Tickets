<?php 
class Admin_Validate_Username extends Zend_Validate_Abstract
{
	/**
     * Error codes
     * @const string
     */
    const USERNAME_BUSY                 = 'usernameBusy';
    
    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::USERNAME_BUSY                     => "",

       );
        
    public function isValid($value)
    {
        return true;
    }    
}