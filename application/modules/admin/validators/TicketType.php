<?php 
class Admin_Validate_TicketType extends Zend_Validate_Abstract
{
	/**
     * Error codes
     * @const string
     */
    const ERROR = 'error';
    
    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::ERROR => "Error",

       );
        
    public function isValid($value)
    {
        $this->_error(self::ERROR);
        return false;
    }    
}