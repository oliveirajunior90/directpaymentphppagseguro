<?php 

class Auth {

    public static function init() {

        $paymentRequest = new PagSeguroPaymentRequest(); 

        try {
            
        $credentials = PagSeguroConfig::getAccountCredentials();
        $sessionId = PagSeguroSessionService::getSession($credentials); 

        echo $sessionId;

        } catch (PagSeguroServiceException $e) {  
            die($e->getMessage());  
        }     
        

    }

}