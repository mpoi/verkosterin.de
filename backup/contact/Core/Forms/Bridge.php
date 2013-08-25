<?php
namespace Core\Forms{ 
    /************************************************************
    * @author Thomas Wicht
    * @link http://www.xqhq.de/
    * @version 1.16
    * @copyright 2012
    * @license for any licence visit my website or write me message
    *
    * Brige for html template and a form. With it you can       
    * manipulate a form, select a value and mark Errors in it   
    *************************************************************/
    class Bridge{
        /**
        * tpl that include the form
        * @var \Core\Tools\Tpl
        */
        protected $tpl;
        /**
        * id of a form that should be manipulated       
        * @var string
        */
        protected $formId = "";
        /**
        * Storage for some error messages
        * @var array 
        */
        protected $error = array();
        /**
        * Generates a Brigde to a specific form in the given html code
        * 
        * @param string &$html
        * @param string $formId if there is more then one form specific a id to fetch the exact form
        * if there a duplicate names for form elements, use a id too to replace the data on exact elements
        * @return Helper
        */
        public function __construct(\Core\Tools\Tpl &$tpl, $formId = ""){
            
            $this->tpl = &$tpl;
            $this->formId = $formId;
        }
        /**
        * fetch a form out of the thml code of current tpl
        * @return string form
        * @throws \Exception if there is no form
        */
        private function FetchFormCode(){
              
            if(!empty($this->formId)){
                $formElement = \Core\Forms\Elements::GetById($this->formId,    $this->tpl->html);    
            }
            else{
                $formElement = \Core\Forms\Elements::GetByTagName("form",$this->tpl->html);     
            }
            
            if($formElement->GetCount() == 0){
                throw new \Exception("unable to fetch form, check your html code");    
            }
            elseif($formElement->GetCount() > 1){
                throw new \Exception($formElement->GetCount() . " forms found, Core\\Forms\\Bridge needs unique formId, current formId: {$this->formId}");      
            }
            $formElement = $formElement->GetElement(0);
            return $formElement['text'];
            
        }
        /**
        * Search all keys of the given array in the form name=key and sets the value or select something
        * @param array $data mostly $_POST || $_GET
        */
        public function ArrayToForm(array $data){
            
            $dstCode = $this->FetchFormCode();
            
            foreach($data as $name => $value){
             
                /// if the value ist a array,
                // the name must extend to html form name[] 
                if(is_array($value)){
                    $name = "{$name}\[\]";    
                }
                // fetch all elements with the given name
                $Elements = \Core\Forms\Elements::GetByName($name,$dstCode);
                
                // if the element was not found in the src html code
                // we skip it, because we can't set the value in this form :)
                if(!$Elements->HasElements()){
                    continue;
                }
                // only fetch the first value, elements with the same name
                // should be the same type of input. its enough data to compare
                $element  = $Elements->GetElement(0);
                $tagName  = $element['tagName'];
                
                // the type is needed because we must know, how to set the value into the diffrent
                // elements, by value, innerHTML, selected, checked
                $type = isset($element['attributes']['type']) ? $element['attributes']['type'] : "";
                
                switch($tagName){
                    
                    /**
                    * input elements
                    */
                    case 'input':    
                        // the type is importent
                        switch($type){
                            
                            case "radio":    
                            case "checkbox": $Elements->WhereAttribute("value",$value)->SetAttribute("checked","checked");
                            break;
                            
                            case "file":     // no idea now whats to do with file
                            break;
                            
                            // all other is default
                            // best way to handle html 5 and unkown parts
                            default: $Elements->SetAttribute("value",$value);
                            break;
                        }
                            
                    break;
                    /**
                    * select
                    */
                    case 'select':   $Elements->SetSelectedValue($value);
                    break;
                    /**
                    * textarea
                    */
                    case 'textarea': $Elements->SetInnerHTML($value);
                    break;
                }     
            }
            $this->ReplaceForm($dstCode);
            return $this;
        }
        /**
        * Replace the form and update the html code 
        * @param string $dstCode
        */
        private function ReplaceForm($dstCode){
            $this->tpl->html = str_replace($this->FetchFormCode(),$dstCode,$this->tpl->html);         
        }
        public function ErrorToString($seperator){
            return implode($seperator,$this->error);    
        }
        /**
        * stores a error and binds them to a html element with given name
        *   
        * @param string $name the name of the error, each name is unique and stores one message
        * @param string $errorMsg
        */
        public function AddError($name,$errorMsg){
            $this->error[$name] = $errorMsg;                
        }
        /**
        * Checks if there are any errors stored
        * @return bool true, else false
        */
        public function HasErrors(){
            
            if(count($this->error)){
                return true;
            }
            return false;
        }  
        /**
        * Setzt die Fehlerliste zurück
        */
        public function ClearError(){
            $this->error = array();
        }
        /**
        * inserts all stored errors, after the element that belongs to the error
        */
        public function PlaceErrorMSGByInput(){
            
            $dstCode = $this->FetchFormCode();
            
            foreach($this->error as  $inputName => $error_msg){   
                
                $directHelpDiv = "<div id=\"direct_help_{$inputName}\" class=\"direct_help\">{$error_msg}</div>";
                Elements::GetByName($inputName,$dstCode)->InsertAfter($directHelpDiv);
            }
            $this->ReplaceForm($dstCode);
            return $this;
        }      
        /**
        * Add a CSS class to all known elements, elements are in self error
        * the name of the elements are the key of the array
        */
        public function MarkInputByError(){
        
            $dstCode  = $this->FetchFormCode();
            $cssClass = "input_error_marker";
            $elements = array();
            
            foreach($this->error as  $inputName => $error_msg){
                $elements[] = $inputName;    
            }
            Elements::GetByName($elements,$dstCode)->AddAttributeValue("class",$cssClass);
            $this->ReplaceForm($dstCode);
            return $this;
        }
        /**
        * add message to id="system_message" and set the css class system_message_ok | system_message_error
        * 
        * @param string $message
        * @param bool $status
        */
        public function SetSystemMessage($message, $status = false){
            
            $this->tpl->InsertSystemMessage($message,$status);             
        }           
    }
}  
?>