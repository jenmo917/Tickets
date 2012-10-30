<?php

class Admin_AjaxController extends Zend_Controller_Action
{   
    /*
    * Get LiU-student information with the help from KOBRA (https://kobra.ks.liu.se).
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	JSON
    */    
    public function getKobraDetailsAction()
    {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
        $kobra = new Generic_Kobra();

		$liuid = $this->getRequest()->getParam('id');
		
        // search by RFID number or LiU-ID
        if ( is_numeric($liuid) )
                $details = $kobra->findByRFID($liuid);
        else
                $details = $kobra->findByLiuID($liuid);

        // Create JSON object
        $json = '';
        if(!empty($details))
        {
                $details['first_name'] = ucfirst(strtolower($details['first_name']));
                $details['last_name'] = ucfirst(strtolower($details['last_name']));
                $json = Zend_Json::encode($details);
        }

		// Tell the browser that we are sending some json data
		header('content-type: application/json');
		
		// Exit and return $json
        exit($json);	
    }    
}

