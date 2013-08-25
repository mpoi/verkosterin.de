<?php
namespace Core\Email{
    /**
    * @author Thomas Wicht
    * @link http://www.xqhq.de/
    * @version 1.01
    * @copyright 2012
    * @license for any licence visit my website or write me message
    * 
    * send a mail with smtp || phpmail
    */        
    class Mailer{
        
        private $mailMode = "smtp";
        private $mail  = null;
        private $smtp  = null;
        
        public function __construct(){ }
        
        public function Send(Mail $mail){
            
            $this->mail = $mail;
            
            switch($this->mailMode){
                case "smtp":    $this->SMTPMail($mail->to, $mail->from);                    break;
                case "phpmail": $this->PHPMail( $mail->to, $mail->subject, $mail->charset); break;
                
                default: return false;
            }               
            return false;
        }
        /**
        * sets the mail mode
        * @param string $mailMode smtp || phpmail
        */
        public function SetMailMode($mailMode){
            
            if(!$this->IsValidMailMode($mailMode)){
                throw new \Exception("invalid mail mode {$mailMode}");
            } 
            $this->mailMode = strtolower($mailMode);          
        }
        
        
        private function InitSMTP(){
            
            if($this->smtp == null){
                $this->smtp = new \Core\Email\Smtp(
                    $GLOBALS['SMTP']['default']['USER'],
                    $GLOBALS['SMTP']['default']['PASS'],
                    $GLOBALS['SMTP']['default']['HOST'],
                    $GLOBALS['SMTP']['default']['PORT'],
                    false
                );          
            }
        }
        
        private function IsValidMailMode($mailMode){
         
            $mailMode = strtolower($mailMode);
            switch($mailMode){
                case 'smtp':     
                case 'phpmail': return true;
                    break;     
            }
            return false;
        }
        
        private function PHPMail($to, $subject, $charset){
            
            $header = $this->mail->GetMail(true);
            
            if(!@mail($to,$this->mail->GetEncodedLineString($subject,$charset,'b'), "",$header)){
                throw new \Exception("Email could not be sent");
            }
        }
        
        private function SMTPMail($to, $from){
        
            if($this->smtp == null){
                $this->InitSMTP();
            }
            $this->smtp->SendMail($to, $from, $this->mail->GetMail());               
        }
    }
}  
?>