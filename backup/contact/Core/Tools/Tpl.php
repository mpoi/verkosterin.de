<?php
namespace Core\Tools{
    /**
    * @author Thomas Wicht
    * @link http://www.xqhq.de/
    * @version 1.46
    * @copyright 2005
    * @license for any licence visit my website or write me message
    * 
    * A Template Class
    */       
    class Tpl{
        
        const system_message_ok    = "system_message_ok";
        const system_message_error = "system_message_error";
        /**
        * Speicherort des aktuellen HTML Codes des Templates  
        * @var string $html  
        */
        public $html = ""; 
        /**
        * @var string $src_bkp Kopie des Templates in der Grundform   
        */
        private $src_bkp = "";   
        /**
        * Wenn true wird ein Fehler ausgegeben wenn versucht wird
        * eine Varibale im Template zu ersetzen die nicht vorhanden ist     
        * @var boolean
        */
        public $config_varNotFoundError = false;
        /**
        * Wenn true werden bei der Ausgabe der Seite alle Pfade zu den
        * Templates mit ausgegeben 
        * @var boolean
        */
        public  $config_showTemplatePath = false;
        /**
        * Enthält alle Strings die für eine bestimmte 
        * Position im Template mit registerContent registriert wurden
        * @var array
        */
        protected $RegisterContent = array();
        
        /**
        * Diese Variablen gehören zum System und sind basics
        * diese werden automatisch unabhängig von jeder Einstellung
        * im Quellcode gelöscht 
        * @var array
        */
        private $system_tpl_vars = array("css","javascript_body","javascript","system_message");
        
        /**
        * path to template src file
        * @var string
        */
        private $fileSrc;
        /**
        * Erzeugt eine Klase zum handeln von Templates
        * @param string der Pfad zur Template Datei 
        * @param string Sprache die zum Cachen des Templates verwendet wird   
        */
        public function __construct($src_path){
            
            $this->html      = "";
            $this->src_bkp = "";
            
            if(file_exists($src_path)){
                
                $this->fileSrc = $src_path;
                    
                $this->html = file_get_contents($src_path);
                $this->src_bkp = $this->html;       
            }
            else{
                trigger_error("Das Template {$src_path} existiert nicht.",E_USER_ERROR);
            }  
            // CSS Style für Ausgabe von Template Pfaden
            $this->css  = "position:relative;";
            $this->css .= "opacity:0.3;"; 
            $this->css .= "filter:Alpha(opacity=30);"; 
            $this->css .= "z-index:500;";
            $this->css .= "color:white;";
            $this->css .= "background-color:red;";
            $this->css .= "border: 1px solid #000000;"; 
            $this->css .= "padding: 1px;";
            $this->css .= "font-family:arial;";
            $this->css .= "font-size:10px;";
            $this->css .= "font-weight:500;";
        }
        /**
        * Vormerken für Inhalt der an eine bestimmte Template 
        * Position gesetzt werden soll. Damit ist es möglich an
        * eine Template Variable mehrere Inhalte zu übermitteln.
        * 
        * Alle Inhalte werden beim zurückgeben des Templates,
        * hintereinander eingefügt 
        * 
        * @since 2.04
        * @param string $tpl_var_name
        * @param string $content
        */
        public function RegisterContent($tpl_var_name,$content){
            
            if(!isset($this->RegisterContent[$tpl_var_name])){
                $this->RegisterContent[$tpl_var_name]            = array();
                $this->RegisterContent[$tpl_var_name]['content'] = array();
            }
            
            $this->RegisterContent[$tpl_var_name]['content'][] = $content;
        }
        /**
        * Setzt alle registrieten Content Bereiche zumsammen
        * und setzt diese ins Template 
        * @since 2.04
        */
        private function InsertRegistredVars(){
            
            if(count($this->RegisterContent) > 0){
                
                foreach($this->RegisterContent as $tpl_var_name => $values){
                    
                    switch($tpl_var_name){
                        /**
                        * CSS Dateien
                        */
                        case 'css':
                        
                            if(count($values['content']) > 0){
                                
                                $content = ""; 
                                
                                foreach($values['content'] as $href){
                                    $content .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$href}\" />\n";    
                                }
                                $this->Replace($tpl_var_name,$content);   
                            } 
                        break; 
                        /**
                        * Javascript Dateien
                        */
                        case 'javascript':
                            if(count($values['content']) > 0){
                                
                                $content = "";
                                
                                foreach($values['content'] as $href){
                                    $content .= "<script type=\"text/javascript\" src=\"{$href}\"></script>\n";    
                                }
                                $this->Replace($tpl_var_name,$content);   
                            }                       
                        break;
                        /**
                        * Javascript Code
                        */
                        case 'javascript_body':
                            if(count($values['content']) > 0){
                                $content = "";
                                
                                foreach($values['content'] as $javascriptCode){
                                    $content .= $javascriptCode;    
                                }
                                $this->Replace($tpl_var_name,"<script type=\"text/javascript\">{$content}</script>\n");   
                            }    
                        break;
                        // Normale String Inhalte
                        default:
                        
                            if(count($values['content']) > 0){
                                $content = implode('',$values['content']);
                                $this->Replace($tpl_var_name,$content);   
                            } 
                        break;   
                    }
                }   
            } 
            $this->RegisterContent = array();  
        }
        /**
        * deletes a node with given name or id
        * @param string $element_name name or id
        */
        public function Delete($element_name){
                
            \Core\Forms\Elements::GetByName($element_name,$this->html)->DeleteNode();
            \Core\Forms\Elements::GetById($element_name,$this->html)->DeleteNode();          
        }
        /**
        * replace some attribute of dom node or set the innerHTML
        * 
        * @param string $element_name the name of the dom node name="abc" or id=""
        * @param string $attribute_name like value, class, id, ... or innerHTML
        * @param string $replace that should set
        * @param bool $trim if the value should be trimed
        * @param int $maxlength 0 for no limit, else there is mb_substr
        * @param bool $strip_tags should tags be striped
        * @param bool $htmlentities 
        */
        public function ReplaceAttr($element_name,$attribute_name, $replace, $trim = false, $maxlength = 0, $strip_tags = false, $htmlentities = false){     
            
            $replace = \Core\Tools\Escape::Object($replace,0,0,$htmlentities, $strip_tags);
            
            if($attribute_name == "innerHTML"){
                \Core\Forms\Elements::GetByName($element_name,$this->html)->SetInnerHTML($replace);
                \Core\Forms\Elements::GetById(  $element_name,$this->html)->SetInnerHTML($replace);
            }
            else{
                \Core\Forms\Elements::GetByName($element_name,$this->html)->SetAttribute($attribute_name,$replace);    
                \Core\Forms\Elements::GetById(  $element_name,$this->html)->SetAttribute($attribute_name,$replace);    
            }
        }                
        /**
        * replace a template variable with the given replacment
        * a tpl var has follow format: {[some_name]}
        * 
        * @param string $search the variable name
        * @param string $Replace the replacment
        * @param bool $strip_tags 
        * @param bool $htmlentities 
        */
        public function Replace($search,$Replace,$strip_tags = false,$htmlentities = false)
        {     
            if($strip_tags == true) { 
                $Replace = @strip_tags($Replace);   
            }
            if($htmlentities == true){ 
                $Replace = @htmlentities($Replace,ENT_COMPAT,CHARSET);   
            }        
            $count = 0;

            $this->html = str_Replace("{[{$search}]}",$Replace,$this->html,$count);
            
            if($this->config_varNotFoundError == true && $count < 1){
                trigger_error("Die Template Variable {[{$search}]} konnte nicht gefunden werden");
            }
        }    
        /**
        * Ersetzt eine Template Variable gegen einen Parameter
        * 
        * @since 1.00
        * @param string die zu ersetzende Variable
        * @param string der zu ersetzende Content
        * @param bool wenn true wird strip_tags auf den Einfügewert angewendet  
        * @param bool wenn true wird htmlentities auf den Einfügewert angewendet  
        */
        public function ReplaceLanguageVar($search,$Replace,$stripTags = false,$htmlentities = false){     
            
            if($stripTags == true){ 
                $Replace = strip_tags($Replace);   
            }
            if($htmlentities == true){ 
                $Replace = htmlentities($Replace,ENT_COMPAT,WEBSITE_CHARSET);   
            }        
            $count = 0;

            $this->html = str_Replace("[[{$search}]]",$Replace,$this->html,$count);
            
            if($this->config_varNotFoundError == true && $count < 1){
                trigger_error("Die Template Variable [[{$search}]] konnte nicht gefunden werden");
            }
        }
        /**
        * Gibt das aktuelle Template zurück 
        * 
        * @since 1.00
        * @param bool wenn true werden alle nicht benutzten Variablen im Template gel?scht 
        * @return string alle Daten des geladenen Templates
        */    
        public function Render($delete_unsed_vars = false){  
          
            if($this->config_showTemplatePath == true){
                $this->html .= "<br />&nbsp;<span style='{$this->css}'>{$this->fileSrc}</span>";       
            }
            
            if(defined('HTTP_HOST')){
                $this->Replace('HTTP_HOST',HTTP_HOST);     
            }
            //$this->html = languageConverter::convertContent($this->html);
            // Doppeltes Parsen um verschachtelte Elemente zu ersetzen
            //$this->html = languageConverter::convertContent($this->html);
            
            // Jeglicher vorgemerkter Inhalt für bestimmte Template Variablen
            // wird jetzt an das Template übergeben
            $this->InsertRegistredVars();
            
            if($delete_unsed_vars === true){
                $this->delete_unsed_vars();    
            }
            
            // System Variablen löschen
            foreach($this->system_tpl_vars as $var){
                $this->html = str_Replace("{[{$var}]}","",$this->html);    
            }
            
            // $this->insert_language(@$GLOBALS['language']);
            return $this->html; 
        }  
        /**
        * Löscht alle Variablen aus dem Template 
        * @since 1.00
        */
        protected function delete_unsed_vars(){ 
            $this->html = preg_Replace('#{\[.*?\]}#im','',$this->html);    
        }
        /**
        * shows the last stored system message 
        */
        public function ShowLastStoredSystemMessage(){
            
            if(isset($_SESSION['systemMessage']) && isset($_SESSION['systemMessageClass'])){
                
                $this->insert_system_message($_SESSION['systemMessage'],$_SESSION['systemMessageClass']);
                
                unset($_SESSION['systemMessage']);
                unset($_SESSION['systemMessageClass']);
            }
        }
        /**
        * inserts a system message to div with id system_message
        * 
        * @param string $systemMessage
        * @param string $systemMessageClass
        */
        public function InsertSystemMessage($systemMessage,$systemMessageClass = 'system_message_error'){
        
            \Core\Forms\Elements::GetById("system_message",$this->html)->
            SetInnerHTML($systemMessage, false)->
            SetAttribute("class",$systemMessageClass);    
        }
        /**
        * Stores a system message in session
        * 
        * @param string $systemMessage
        * @param string $systemMessageClass
        */
        public function StoreSystemMessage($systemMessage,$systemMessageClass = 'system_message_error'){
            $_SESSION['systemMessage']      = $systemMessage;   
            $_SESSION['systemMessageClass'] = $systemMessageClass;   
        }
        /**
        * Ein Array wird anhand seiner Key => Value Werte ins Template eingefügt
        * der Key bildet den Variablen Namen, Value dessen Wert
        * 
        * @since 1.00
        * @param array assoc
        * @param bool wenn true wird strip_tags auf den Einfügewert angewendet  
        * @param bool wenn true wird htmlentities auf den Einfügewert angewendet 
        */
        public function ReplaceVarsByArray($array_values,$strip_tags = false,$htmlentities = false){
            if(is_array($array_values) || is_object($array_values))
            {
                foreach($array_values as $k => $v)
                {
                    if(is_string($v) || is_numeric($v))
                    {
                        $this->Replace($k,$v,$strip_tags,$htmlentities);               
                    }
                    elseif(is_array($v) || is_object($v))
                    {
                        $subValue = array();
                        
                        foreach($v as $value)
                        {
                            if(is_string($value) || is_numeric($value)) 
                            {
                                $subValue[] = $value;    
                            }        
                        } 
                        $this->Replace($k,implode(', ',$subValue),$strip_tags,$htmlentities);   
                    }
                }
            }
        }
        /**
        * Zurücksetzung des Templates in den Urzustand
        * 
        * @since 1.00
        */
        public function ResetTpl(){    
            $this->html = $this->src_bkp;
        }        
    }
    
}
?>