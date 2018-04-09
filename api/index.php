<?php
error_reporting(E_ALL);
ini_set('display_errors', -1);

require 'config.php';
require __DIR__ . '/vendor/autoload.php';

require '/action/DirectPaymentRequest.php';
require 'action/Auth.php';

$klein = new \Klein\Klein();

$klein->respond('GET', '/auth', function(){ 
    Auth::init();
});

$klein->respond('POST', '/payment-request', function($request, $response){ 
    ////$_POST;
    DirectPaymentRequest::main($_POST); 
});

$klein->dispatch();
