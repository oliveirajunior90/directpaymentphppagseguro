<?php 

$base  = dirname($_SERVER['PHP_SELF']);
// Update request when we have a subdirectory    
if(ltrim($base, DIRECTORY_SEPARATOR)){ 
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($base));
}
