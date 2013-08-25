<?php
/**
* @author Thomas Wicht
* @link http://www.xqhq.de/
* @version 1.05
* @copyright 2005
* @license for any licence visit my website or write me message
*/
include("config.system.php");
define('CAPTCHA_FONT',REAL_PATH.'font/mgmdIta_.ttf');
/**
* background color
* red   0 - 255
* green 0 - 255 
* blue  0 - 255   
*/
define("SECURITY_IMAGE_BG_R",255); 
define("SECURITY_IMAGE_BG_G",255);
define("SECURITY_IMAGE_BG_B",255);
/**
*Farbeinstellung Captcha Grafik Schrift 1
*@var int SECURITY_IMAGE_FONT_FRONT_R Schriftfarbanteil Rot 0 - 255 
*@var int SECURITY_IMAGE_FONT_FRONT_G Schriftfarbanteil Gr?n 0 - 255
*@var int SECURITY_IMAGE_FONT_FRONT_B Schriftfarbanteil Blau 0 - 255 
*/
define("SECURITY_IMAGE_FONT_FRONT_R",000);
define("SECURITY_IMAGE_FONT_FRONT_G",000);
define("SECURITY_IMAGE_FONT_FRONT_B",000); 
/**
*Farbeinstellung Captcha Grafik Schrift 2
*@var int Schriftfarbanteil Rot 0 - 255
*@var int Schriftfarbanteil Gr?n 0 - 255
*@var int Schriftfarbanteil Blau 0 - 255   
*/
define("SECURITY_IMAGE_FONT_BACK_R",000);   
define("SECURITY_IMAGE_FONT_BACK_G",000); 
define("SECURITY_IMAGE_FONT_BACK_B",122);   
/**
*Einstellungen der Captcha Zeichenl?nge und Zeichen
*@var int CAPTCHA_LENGTH Captcha L?nge in Zeichen
*/
define("CAPTCHA_LENGTH",3);
/**
*Captcha Modus - dieser bestimmt ob Zahlen, 
*@var string CAPTCHA_MODUS MIXED , NUMERIC , LOWER_ALPHA ,UPPER_ALPHA
*/
define("CAPTCHA_MODUS","UPPER_ALPHA");

@session_start();
header("Content-type: image/jpeg");

$im = new randomImage(15,20,CAPTCHA_LENGTH);

$_SESSION['captcha'] = $im->text;

print $im->getPicture();

// -------------------------------------------------------
/**
* @desc Klasse zum erzeugen eines zuf?lligen Bildes
*/
class randomImage
{        
/**
* @desc Konstruktor der Klasse, dieser ?bernimmt die Erzeugung des Bildes
* @param string Pfad zum Hintergrundbild 
* @param int H?he der Schrift in Pixel
* @param int Breite der Schrift die ?bergeben wird
* @param int Anzahl der zu verwendenden Zeichen           
*/
function randomImage($fontHoehe,$fontWeite,$anzahlBuchstaben)
{
    $this->image = imagecreatetruecolor($anzahlBuchstaben*23,25);
    
    //Farben
    $background  = imagecolorallocate ($this->image,SECURITY_IMAGE_BG_R,         SECURITY_IMAGE_BG_G,        SECURITY_IMAGE_BG_B );
    $font_front  = imagecolorallocate ($this->image,SECURITY_IMAGE_FONT_FRONT_R, SECURITY_IMAGE_FONT_FRONT_G,SECURITY_IMAGE_FONT_FRONT_B);
    $font_back   = imagecolorallocate ($this->image,SECURITY_IMAGE_FONT_BACK_R,  SECURITY_IMAGE_FONT_BACK_G, SECURITY_IMAGE_FONT_BACK_B);   
    
    imagefill($this->image,0,0,$background);    
    
    $this->iheight = imagesy($this->image);
    $this->ibreite = imagesx($this->image);
    
    //Schrift    
    $this->fhoehe  = $fontHoehe;
    $this->fweite  = $fontWeite;
    
    if(file_exists(CAPTCHA_FONT))
    {
        $this->font    = CAPTCHA_FONT; 
    }
    else{$this->font = "../".CAPTCHA_FONT;}
    
    //Anzahl der Zeichen    
    $this->$anzahlBuchstaben = $anzahlBuchstaben;
    
    //Wir erzeugen einen zuf?lligen String    
    $this->text = $text = $this->randomString($this->$anzahlBuchstaben,CAPTCHA_MODUS);
    
    //Wir spalten den String in einzelkne Buchstaben
    $buchstaben = preg_split('//',$this->text) ;
    
    //Der Abstand der Buchstaben    
        $spacing = -20;
        
    //alle Buchstaben in der Schleife durchlkaufen    
    foreach($buchstaben as $buchstabe)
    {
        $winkelmodus = rand(1,2);

        if($winkelmodus == 1 ){$angle = rand(0,25);}else{$angle = rand(335,360);}     
        
        imagettftext($this->image,$this->fhoehe+1,$angle,$spacing+1,(($this->iheight + $this->fhoehe) / 2),$font_back,$this->font,$buchstabe);
        imagettftext($this->image,$this->fhoehe,$angle,$spacing,(($this->iheight + $this->fhoehe) / 2),$font_front,$this->font,$buchstabe);                                                                                                         
        $spacing += $this->fweite + 3;
    }                
}

   function getPicture()
   {
        return imagejpeg($this->image,null,100);
   }

/**
* @desc Funktion zum erzeugen eines zuf?lligen Strings
* @param int L?nge des Strings
* @param string modus MIXED, NUMERIC, LOWER_ALPHA, UPPER_ALPHA
* @return string 
*/
    function randomString($length,$modus = 'MIXED')
    {    
        $alpha_lc = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w');
        $alpha_uc = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W');
        $number   = range('0','9');
        
        $code     = "";
        
        switch($modus)
        {
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

        do
        {
            $letter = rand(0,$count);
            $code .= $alpha[$letter];
        }
        while(strlen($code) < $length);
        
        return $code;
    }
    public static function hex2dec($hex) {
        $color = str_replace("#", "", $hex);
        $ret = array(
            "r" => hexdec(substr($color, 0, 2)),
            "g" => hexdec(substr($color, 2, 2)),
            "b" => hexdec(substr($color, 4, 2))
        );
        return $ret;
    }
}  
?>