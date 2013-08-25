<?php
namespace Core\Email{
    /**
    * @author Thomas Wicht
    * @link http://www.xqhq.de/
    * @version 1.18
    * @copyright 2010
    * @license for any licence visit my website or write me message
    * 
    * access class for a smtp connection to send mails
    */        
    class Smtp{
        
        private $username;
        private $password;
        private $host;
        private $port;
        private $connection = null;
        
        // Response 
        private $response;
        private $responseCode;
        private $debug;

        public function __construct($username,$password, $host, $port,$debug = false){
            
            $this->host     = $host;
            $this->port     = $port;
            $this->username = $username;
            $this->password = $password;
            
            $this->debug = $debug;
            
            $this->Connect();
        }
        
        private function Connect(){
            
            if(is_resource($this->connection)){
                return;
            }
            
            if(!function_exists('fsockopen')){          
                throw new \Exception("function fsockopen is not installed",E_USER_ERROR); 
                return false;
            }
             
            $error = "";
            $errno = "";
            $ssl = "";
            
            if($this->port == 465 || $this->port == 587){
                $ssl = "ssl://";
            }
            
            if(!$this->connection = @fsockopen($ssl.$this->host,$this->port,$errno,$error,10)){  
                throw new \Exception("SMTP connection error:".trim($error),E_USER_ERROR);
                return false;   
            }
            
            $this->GetResponse();
            
            if($this->responseCode != 220){
                throw new \Exception("SMTP return code != 220: {$this->response}",E_USER_ERROR); 
            }
            // Begrüßung und lesen der Response 250-
            $this->SendCommand("EHLO {$_SERVER['HTTP_HOST']}");
            
            if($this->responseCode != 250){
                throw new \Exception("SMTP return code != 250:{$this->response}",E_USER_ERROR); 
            }
            
            // Server Informationen lesen
            // Auth Mehtoden speichern
            $auth_types = "";
            do{
                $this->GetResponse(); 
                if(preg_match("#(250[-\s])auth\s.+#i",$this->response,$match))
                {
                    $auth_types = str_replace($match[1],'',$this->response);
                }
            }
            while(preg_match("#250-#",$this->response));
            
            if(strlen($auth_types) == 0)
            {
                throw new \Exception("no valid auth methods found for a smtp connection",E_USER_ERROR);  
            }
            
            if(preg_match("#CRAM-MD5#i",$auth_types)){
                $this->SendCommand("AUTH CRAM-MD5");
                $this->throwException(334,$this->response);
                
                $clientDigest = $this->CRAM_MD5_DIGEST();
                $this->SendCommand(base64_encode("{$this->username} {$clientDigest}"));    
            }
            elseif(preg_match("#PLAIN#i",$auth_types)){
                $this->SendCommand("AUTH PLAIN");
                $this->throwException(334,$this->response);
                
                $this->SendCommand(base64_encode("\000{$this->username}\000{$this->password}"));
            }
            elseif(preg_match("#LOGIN#i",$auth_types)){
                $this->SendCommand("AUTH LOGIN");
                $this->throwException(334,$this->response);
                
                $this->SendCommand(base64_encode("{$this->username}"));
                $this->SendCommand(base64_encode("{$this->password}"));
            }
            else{
                throw new \Exception("SMTP class has no valid auth method for this SMTP Server, valid are: CRAM-MD5, PLAIN, LOGIN",E_USER_ERROR);     
            }
            
            if($this->responseCode != 235){
                throw new \Exception("SMTP: {$this->response}",E_USER_ERROR);     
            }
        }
        
        private function throwException($code,$message){
            if($this->responseCode != $code){
                throw new \Exception($message);
            }
        }
        private function CRAM_MD5_DIGEST(){
            $serverDigest = trim(substr( $this->response,4));
            $clientDigest = hash_hmac('MD5',base64_decode($serverDigest),$this->password); 
            
            return $clientDigest;
        }
        
        public function SendMail($to, $from, $email,$try = 1){
            
            $this->SendCommand("MAIL FROM: <{$from}>");
            
            // Wenn die Verbindung verloren geht, 
            // einmal neu versuchen eine Verbidung zum SMTP aufzubauen
            if($this->responseCode != 250)
            {
                if($try == 0){
                    throw new \Exception("Email could not be sent",E_USER_ERROR);
                }
                
                $this->Connect();
                $this->SendMail($to, $from, $email,0); 
                return true;  
            }
            
            $this->SendCommand("RCPT TO: <{$to}>"); 
            $this->SendCommand("DATA");              
            $this->SendCommand("{$email}\r\n.");
            
            if($this->responseCode == 250){
                return true;
            }
            throw new \Exception("Email could not be sent: {$this->responseCode}: {$this->response}",E_USER_ERROR);
        }
        
        private function SendCommand($command){
            
            if($this->debug){
                print "ME: <pre>".htmlentities($command)."</pre><br/>";
            }
            fputs($this->connection,$command."\r\n");
            $this->GetResponse(); 
        }
        
        public function GetResponse(){
            
            $this->response     = fgets($this->connection);
            $this->responseCode = substr($this->response,0,3); 
            
            if($this->debug){
                print "SMTP: <pre>".$this->response."</pre><br/>";flush();      
            } 
        }  
    }    
}
?>