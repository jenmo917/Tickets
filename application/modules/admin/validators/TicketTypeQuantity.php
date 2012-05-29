<?php 
class Admin_Validate_TicketTypeQuantity extends Zend_Validate_Abstract
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
        self::ERROR => "Please enter a number.",
       );
        
    public function isValid($value, $context = null)
    {
        unset($context['quantity']);
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