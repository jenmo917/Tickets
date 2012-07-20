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
        $kobra = new Generic_Kobra();

        // Exit if liuit is empty
        if ( empty($_GET['liuid']) )
                exit();

        // Change var
        $liuid = $_GET['liuid'];

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
        // Exit and return $json
        exit($json);
    }    
}

