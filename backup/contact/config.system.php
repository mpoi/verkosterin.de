<?php
@session_start();
// your name
define('ADMIN_NAME', 'Thomas Wicht');  
// your email
define('ADMIN_EMAIL','thomas.wicht@xqhq.de'); 
// sender adress of email copy to user
define('BLIND_EMAIL','thomas.wicht@xqhq.de');
/**
* E-Mails can be send with phpmail or smtp
*/
define('MAIL_MODE','phpmail');
// ---------------------------------------------------------------------
// # SMTP Connection Data, leave blank or leave the default values if you send with phpmail   
$GLOBALS['SMTP']['default']['USER']    = "some@example.com";
$GLOBALS['SMTP']['default']['PASS']    = "your_password";
$GLOBALS['SMTP']['default']['HOST']    = "example.com";
$GLOBALS['SMTP']['default']['PORT']    = 25;
$GLOBALS['SMTP']['default']['DEBUG']   = false;  
// ---------------------------------------------------------------------
// Charset
define('CHARSET','UTF-8'); 
@header("content-type: text/html; charset=".CHARSET);  
// END OF LINE used by email headers
// use \r\n or \n
define('EOL',"\n"); 
// --------------------------------------------------------------------- 
// timezone
// --------------------------------------------------------------------- 
date_default_timezone_set('Europe/Berlin');
/**
* use the script below to print the real path 
* create a file with a editor and copy paste the code and browse it
* 
* <php print realpath("."); ?>
*/
// path to contact_form directory on your server
// something like 
// d:/contact_form/
// /var/something/contact_form/
// you only need this if you want to include the form

define('REAL_PATH',mb_substr(realpath(__FILE__),0,strrpos(realpath(__FILE__),"/"))."/"); 

















// *********************************************************************
// don't modify anything below this line
// *********************************************************************
error_reporting (E_ALL);
header("content-type: text/html; charset=".CHARSET);  
mb_internal_encoding(CHARSET);
ini_set('mbstring.internal_encoding',CHARSET); 

mb_internal_encoding(CHARSET);
ini_set('mbstring.internal_encoding',CHARSET); 

function __CoreClassLoader($name){
    
    $name = str_replace("\\","/",$name.".php");
    
    if(!file_exists(REAL_PATH.$name)){
        if(strstr($name,"Core")){
            print "Sorry, there is a Core class missing: ".REAL_PATH.$name; 
        }
        else{
            print "autoload class failed, unable to find route to ".REAL_PATH.$name;      
        }
        exit();    
    }
    else{
        require(REAL_PATH.$name);
    }
}
spl_autoload_register('__CoreClassLoader'); 
?>