<?php
namespace Core\Tools{
    
    class Functions{
        /**
        * Marks every CHAR from needle in haystack
        * 
        * @version 1.00
        * 
        * @param string $needle
        * @param string $haystack
        * @param string $marker example <strong>%s</strong>
        * 
        * @return string 
        */
        public static function CharMarker($needle, $haystack, $marker){
            
            $char_array_needle       = preg_split('/(?<!^)(?!$)/u', strtolower($needle));
            $char_array_haystack     = preg_split('/(?<!^)(?!$)/u', strtolower($haystack)); 
            $char_array_haystack_org = preg_split('/(?<!^)(?!$)/u',  $haystack);
            
            $match = array();
            $count = count($char_array_haystack);
            
            for($i = 0;$i < $count; $i++){
                                          
                if(in_array($char_array_haystack[$i],$char_array_needle) !== false){
                    $match[$i] = true;    
                }
                else{
                    $match[$i] = false;  
                }
            }
            $open    = false;
            $chars   = array();
            $new_str = "";
            
            for($i = 0;$i < count($match); $i++){
                
                if($match[$i]){
                    $open    = true;    
                    $chars[] = $char_array_haystack_org[$i];
                }
                else{
                    
                    if($open){
                        $new_str .= sprintf($marker,implode("",$chars));
                        $open     = false;
                        $chars    = array();    
                    }
                    
                    $new_str .= $char_array_haystack_org[$i];    
                }
            }
            if($open){
                $new_str .= sprintf($marker,implode("",$chars));    
            }
            return $new_str;
        }         
        /**
        * convert a hex number to rgb
        * @param string $hex
        * @return assoc array(r,g,b)
        */
        public static function hex2rgb($hex) {
            $color = str_replace("#", "", $hex);
            $ret = array(
                "r" => hexdec(substr($color, 0, 2)),
                "g" => hexdec(substr($color, 2, 2)),
                "b" => hexdec(substr($color, 4, 2))
            );
            return $ret;
        }        
        /**
        * generate a css table row class to seperate table rows
        * @param int $i
        */
        public static function GetTableRowClass($i){
            $rowClass = $i % 2 == 0 ? 'tableRow_1' : 'tableRow_2';
            return $rowClass;
        }        
        /**
        * validate a given email
        * 
        * @param string $email
        * @return true oder false
        */
        public static function IsValidEmail($email){   
     
            // set of disallowed string
            // example @gmx.net
            $disallowed = array();
            
            foreach($disallowed as $denied){
                if(preg_match("#({$denied})+#i",$email)){ return false;}
            }   

            $allowedLetters = '-_0-9a-z!#';
            $pattern = "/[$allowedLetters]+(?:\.[$allowedLetters]+)*@((?:[$allowedLetters]){3,}\.(?:[$allowedLetters]{2,}))+/i";
           
            if(preg_match($pattern,$email,$match) == 1){
                return true;
            }
            return false;
        }         
        /**
        * generate a random string
        * @param int $length of the random string
        * @param string $modus MIXED, NUMERIC, LOWER_ALPHA, UPPER_ALPHA
        * @return string 
        */
        public static function RandomString($length,$modus = 'MIXED'){  
          
            $alpha_lc = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w');
            $alpha_uc = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W');
            $number   = range('0','9');
            
            $code     = "";
            
            switch($modus){
                
                case 'MIXED':       $alpha = array_merge($alpha_lc,$alpha_uc,$number); 
                    break;
                case 'NUMERIC':     $alpha = array_merge($number); 
                    break;
                case 'LOWER_ALPHA': $alpha = array_merge($alpha_lc); 
                    break;
                case 'UPPER_ALPHA': $alpha = array_merge($alpha_uc); 
                    break;
                default: $alpha = array_merge($alpha_lc,$alpha_uc,$number);  
            }

            $count = count($alpha)-1;

            do{
                $letter = rand(0,$count);
                $code .= $alpha[$letter];
            }
            while(strlen($code) < $length);
            
            return $code;
        } 
        /**
        * checks a number for decimal spelling
        * 
        * @param number $number
        * @param count $decimal signs
        * @return true if it is a decimal value
        */
        public static function IsDecimal($number,$decimal){
            
            $pattern = '#^-?(([0-9]+)|([0-9]+\.{1}[0-9]{0,'.$decimal.'}))$#'; 
            
            if(!is_numeric($number) || !preg_match($pattern,$number) ){
                return false;
            }
            return true;
        }
        /**
        * convert a number from bytes to kilobytes and so on
        * 
        * @param string $from_unit k,m or g
        * @param string $to_unit   k,m or g
        * @param float  $value
        * @throws \Exception 
        */
        public static function ConvertUnitSize($from_unit,$to_unit,$value){
        
            $from_unit = strtolower($from_unit);
            $to_unit   = strtolower($to_unit);
            $result    = false;
            
            switch($from_unit){
            // byte    
                case 'b':
                    switch($to_unit){
                        case 'b': $result = $value;                        break; 
                        case 'k': $result = $value / 1024;                 break; 
                        case 'm': $result = $value / 1024 / 1024;          break;        
                        case 'g': $result = $value / 1024 / 1024 / 1024;   break;        
                    }
                    break;
            // kilobyte            
                case 'k':
                    switch($to_unit){
                        case 'b': $result = $value * 1024;                 break; 
                        case 'k': $result = $value;                        break; 
                        case 'm': $result = $value / 1024;                 break;        
                        case 'g': $result = $value / 1024 / 1024;          break;        
                    }
                break;    
            // megabyte            
                case 'm':
                    switch($to_unit){
                        case 'b': $result = $value * 1024 * 1024;          break; 
                        case 'k': $result = $value * 1024;                 break; 
                        case 'm': $result = $value;                        break;        
                        case 'g': $result = $value / 1024;                 break;        
                    }
                break;    
            // gigabyte            
                case 'g':
                    switch($to_unit){
                        case 'b': $result = $value * 1024 * 1024 * 1024;   break; 
                        case 'k': $result = $value * 1024 * 1024;          break; 
                        case 'm': $result = $value * 1024;                 break;           
                        case 'g': $result = $value;                        break;        
                    }
                break;                
            }
            if($result === false){
                $error  = "unable to convert filesize (from_unit {$from_unit} to_unit {$to_unit}), missing configuration in<br/>";
                $error .= __file__." line " .__LINE__;
                throw new Exception($error);        
            }
            return $result;
        }
        /**
        * trys to get the file extension of the given filename
        * @param string $filename
        * @return string extension
        */
        public static function GetFileExtension($filename){ 
          
            $extensionPos = strrpos($filename,'.');
            $extension    = "";
            
            if($extensionPos > 0){
                $extension = substr($filename,$extensionPos);      
            }
            $extension = mb_strtolower($extension);
            return $extension;
        }                        
    }
}
?>