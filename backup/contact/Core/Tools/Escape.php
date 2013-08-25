<?php
namespace Core\Tools{
    
    class Escape{
        
        const encoding = CHARSET;
        /**
        * @param mixed   $mixed 
        * @param string  $trim
        * @param string  $maxlength 0 == nolimit
        * @param string  $htmlentities
        * @param string  $striptags
        */
        public static function Object($mixed, $trim, $maxlength = 0, $htmlentities = 0, $striptags = 0){
            
            if(is_array($mixed)){
                
                foreach($mixed as $key => $value){
                    
                    $value = self::Object($value, $trim, $maxlength, $htmlentities, $striptags); 
                    $mixed[$key] = $value; 
                }
            }
            elseif(is_string($mixed) || is_numeric($mixed)){
                
                $mixed = $trim         == 1 ? trim($mixed)                                                                      : $mixed;
                $mixed = $htmlentities >= 1 ? htmlentities($mixed,ENT_COMPAT,self::encoding, $htmlentities == 2 ? true : false) : $mixed;
                $mixed = $striptags    == 1 ? strip_tags($mixed)                                                                : $mixed;              
                
                if($maxlength > 0){
                    $mixed = mb_substr($mixed,0,$maxlength,self::encoding);    
                }            
            } 
            return $mixed;
        }
        /**
        * escaping of a string / wall of text that can be used in javascript in a single row without errors
        * 
        * @param mixed $value
        */
        public static function Javascript($value){
        
            // Grundlegendes escapen von " und '
            $value = str_replace('"','\\"',$value);
            $value = str_replace("'","\\'",$value);
            // Ersetzen von Windowszeilenumbrüchen zu \n
            $value = str_replace("\r\n","\n",$value);
            $value = str_replace("\r",  "\n",$value);
            // Alle Zeilen zu einer Zeile zusammenfüggen um JS error zuvermeiden                    
            $values = explode("\n",$value);
            // Escaping des Zeilenumbruches
            $value  = implode("\\n",$values); 
            return trim($value);    
        }         
    }     
}
?>