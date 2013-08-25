<?php
namespace Core\Email{
    /**
    * @author Thomas Wicht
    * @link http://www.xqhq.de/
    * @version 1.01
    * @copyright 2011
    * @license for any licence visit my website or write me message
    * 
    * Generate a E-Mail
    */    
    class Mail{
        
        public  $eol           = EOL;
        private $mail          = "";
        
        public  $subject       = "";
        public  $body_html     = "";
        public  $body_text     = "";
        
        public  $charset       = "UTF-8";

        private $boundary      = "";
        private $boundarySub   = "";
        private $attachments   = ""; // Enthaelt alle E-Mail Anhaenge
        
        public $from_name      = "";  
        public $from           = "";
        public $return_path    = "";
        
        public $to = "";
        
        public function __construct($to, $from, $from_name, $return_path, $subject, $body_html, $body_text, $charset){
            
            $this->to          = $to;
            $this->from        = $from;
            $this->return_path = empty($return_path) ? $from : $return_path;
            $this->from_name   = $from_name;
            $this->subject     = $subject;
            $this->body_html   = $body_html;
            $this->body_text   = preg_replace("#\r\n#","\n",$body_text);
            $this->charset     = $charset;

            $this->boundary    = "--main--".md5(time() * 2 / 6);
            $this->boundarySub = "--sub--".md5(time() * 2 / 6);
        }
        
        /**
        * append a attachment to the mail
        * @param string $filename the name of the attachment
        * @param string $attachment as string   
        */
        public function AppendAttachment($filename, $attachment){
            
            if(empty($filename)){
                throw new \Exception("mail attachment name is empty");
            }
            
            if(empty($attachment)){
                throw new \Exception("mail attachment is empty");
            }
            
            $attachment = chunk_split(base64_encode($attachment));   
            
            $this->attachments .= "--".$this->boundary.$this->eol; 
            $this->attachments .= "Content-Type: application/octet-stream; name=\"".self::GetEncodedLineString($filename,$this->charset,"b")."\"".$this->eol;  
            $this->attachments .= "Content-Transfer-Encoding: base64".$this->eol; 
            $this->attachments .= "Content-Disposition: attachment; filename=\"".self::GetEncodedLineString($filename,$this->charset,"b")."\"".$this->eol.$this->eol; 
            $this->attachments .= $attachment .$this->eol;     

            unset($filename, $attachment);
            return $this;   
        }
        /**
        * generate the email as text
        * @param bool $asPhpMail the headerlines to and subject a stripped out of the header
        */
        public function GetMail($asPhpMail = false){ 
            
            if(!empty($this->attachments)){
                $contentType = "multipart/attachment";      
            }
            elseif(!empty($this->body_html) && !empty($this->body_text)){
                
                $contentType = "multipart/alternative";  
            }
            elseif(!empty($this->body_html) && empty($this->body_text)){
                $contentType = "text/html";     
            }        
            elseif(empty($this->body_html) && !empty($this->body_text)){
                $contentType = "text/plain";     
            }
            else{
                throw new \Exception("unable to send a empty mail");
            }
        
            switch($contentType){
                
                case 'text/plain':
                
                    $header  = "Content-Type: text/plain; charset={$this->charset} {$this->eol}";
                    $header .= "From: ".self::GetEncodedLineString($this->from_name,$this->charset,"b")." <{$this->from}>{$this->eol}";
                    
                    if(!$asPhpMail){
                        
                        $header .= "To: <{$this->to}>  {$this->eol}";
                        $header .= "Subject: ".self::GetEncodedLineString($this->subject,$this->charset,"b")."{$this->eol}";                        
                    }
                    $header .= "Return-Path: {$this->return_path} {$this->eol}{$this->eol}"; 
                    $header .= strip_tags($this->body_text); 
                    break;
                    
                case 'text/html':
                    $header  = "Content-Type: text/html; charset={$this->charset} {$this->eol}";
                    $header .= "From: \"".self::GetEncodedLineString($this->from_name,$this->charset,"b")."\" <{$this->from}>{$this->eol}";
                    
                    if(!$asPhpMail){
                        
                        $header .= "To: <{$this->to}>  {$this->eol}";
                        $header .= "Subject: ".self::GetEncodedLineString($this->subject,$this->charset,"b")."{$this->eol}";                        
                    }
                    
                    $header .= "Return-Path: <{$this->return_path}> {$this->eol}{$this->eol}";
                    $header .= ($this->body_html);  
                    break;
                    
                case 'multipart/alternative': 
                
                    $header  = "MIME-Version: 1.0 {$this->eol}";
                    $header .= "Content-Type: multipart/alternative; boundary=\"{$this->boundary}\"{$this->eol}";  
                    $header .= "From: \"".self::GetEncodedLineString($this->from_name,$this->charset,"b")."\" <{$this->from}>{$this->eol}";
                    
                    if(!$asPhpMail){
                        
                        $header .= "To: <{$this->to}>  {$this->eol}";
                        $header .= "Subject: ".self::GetEncodedLineString($this->subject,$this->charset,"b")."{$this->eol}";                        
                    }
                       
                    $header .= "Return-Path: <{$this->return_path}>{$this->eol}{$this->eol}"; 
                    $header .= "--{$this->boundary}{$this->eol}"; 
                    $header .= "Content-Type: text/html; charset={$this->charset}{$this->eol}{$this->eol}"; 
                    $header .= ($this->body_html) ." {$this->eol}{$this->eol}"; 
                    $header .= "--{$this->boundary}{$this->eol}";
                    $header .= "Content-Type: text/plain; charset={$this->charset}{$this->eol}{$this->eol}"; 
                    $header .= "{$this->body_text} {$this->eol}{$this->eol}";             
                    $header .= "--{$this->boundary}--{$this->eol}{$this->eol}";                           
                    break;
                /**
                * Versand einer multipart/mixed Mail mit Subtype alternative
                * und E-Mail Anhang  
                * http://www.ietf.org/rfc/rfc2046.txt page 26
                */
                case 'multipart/attachment': 

                    $header  = "MIME-Version: 1.0 {$this->eol}";
                    $header .= "From: \"".self::GetEncodedLineString($this->from_name,$this->charset,"b")."\" <{$this->from}>{$this->eol}";
                    if(!$asPhpMail){
                        
                        $header .= "To: <{$this->to}>  {$this->eol}";
                        $header .= "Subject: ".self::GetEncodedLineString($this->subject,$this->charset,"b")."{$this->eol}";                        
                    }
                    $header .= "Return-Path: <{$this->return_path}> {$this->eol}"; 
                    $header .= "Content-Type: multipart/mixed; boundary=\"{$this->boundary}\"{$this->eol}{$this->eol}";   
                    $header .= "--{$this->boundary}{$this->eol}"; 
                        
                        // Wurde Text und HTML angegeben wird ein unterbereich erzeugt, getrennt mit dem sub bondary
                        if(!empty($this->body_html) && !empty($this->body_text)){
                            $header .= "Content-Type: multipart/alternative; boundary=\"{$this->boundarySub}\"{$this->eol}{$this->eol}";  
                            
                            $header .= "--{$this->boundarySub}{$this->eol}"; 
                            $header .= "Content-Type: text/plain; charset={$this->charset}{$this->eol}{$this->eol}";  
                            $header .= strip_tags($this->body_text) ." {$this->eol}{$this->eol}";
                            
                            $header .= "--{$this->boundarySub}{$this->eol}";
                            $header .= "Content-Type: text/html; charset={$this->charset}{$this->eol}{$this->eol}"; 
                            $header .= "{$this->body_html}{$this->eol}{$this->eol}";           
                            
                            // END sub (Ending --)
                            $header .= "--{$this->boundarySub}--{$this->eol}";                                                                        
                        }
                        // Ansonsten wird die normale Trennung beibehalten
                        // und der jeweilige Teil, html oder text angefügt
                        else{
                            
                            if(!empty($this->body_html)){
                                $header .= "Content-Type: text/html; charset={$this->charset}{$this->eol}{$this->eol}"; 
                                $header .= "{$this->body_html}{$this->eol}{$this->eol}";                            
                            }                        
                            if(!empty($this->body_text)){
                                $header .= "Content-Type: text/plain; charset={$this->charset}{$this->eol}{$this->eol}";  
                                $header .= strip_tags($this->body_text) ." {$this->eol}{$this->eol}";                         
                            }
                        }                                     
                        
                    $header .= $this->attachments;
                    $header .= "--{$this->boundary}--{$this->eol}";   
                    break; 
            }
            return $header;
        }
        /**
        * Encodiert einen String nach RFC-2047 der in Headerzeilen wie subject verwendet werden soll
        * und Zeichen außerhalb des ASCII enthält
        * 
        * @since 1.03
        * @param string $string
        * @param string $charset  des Wertes $string
        * @param string $encoding q für quoted printable oder b für base64
        * @return string der codierten Zeile
        */
        public static function GetEncodedLineString($string,$charset,$encoding = "b"){
            
            if(strtolower($encoding) == 'b'){
                $string = base64_encode($string);    
            }
            
            $converted = "=?{$charset}?{$encoding}?".str_replace(" ","=20",$string)."?=";    
            return $converted;
        }
    }  
}
?>