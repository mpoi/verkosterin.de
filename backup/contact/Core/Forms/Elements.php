<?php
namespace Core\Forms{ 
    /**
    * @author Thomas Wicht
    * @link http://www.xqhq.de/
    * @version 1.14
    * @copyright 2011
    * @license for any licence visit my website or write me message
    * some dom manipulation and form helper class
    */
    class Elements{  
        /**
        * never change anything in this file without spending hours in this debug mode :)
        */
        const DEBUG_MODE = false;  
        /**
        * pattern for matching tags by attribute  
        */
        const PATTERN_BY_ATTRIBUTE_STRICT  = "#<(?:(?!>).)*?[[ATTRIBUTE_NAME]]=\"[[NAME]]\"(?:(?!>).)*?>#musx";
        const PATTERN_BY_ATTRIBUTE_LIKE    = "#<(?:(?!>).)*?[[ATTRIBUTE_NAME]]=\"(?:(?!\").)*?[[NAME]](?:(?!\").)*?\"(?:(?!>).)*?>#musx";
        const PATTERN_BY_ATTRIBUTE_LLIKE   = "#<(?:(?!>).)*?[[ATTRIBUTE_NAME]]=\"(?:(?!\").)*?[[NAME]]\"(?:(?!>).)*?>#musx";
        const PATTERN_BY_ATTRIBUTE_RLIKE   = "#<(?:(?!>).)*?[[ATTRIBUTE_NAME]]=\"[[NAME]](?:(?!\").)*?\"(?:(?!>).)*?>#musx";
        /**
        * Get the Element by tag name
        */        
        const PATTERN_TAG_NAME = "#<[[NAME]](?:(?!>).)*?>#musx";
        /**
        * Store for all elemnts that be found
        * @var array
        */
        private $elements = array();
        /**
        * stores a reference for the current content that will be modified
        * @var string
        */
        public $content = ""; 
        /**
        * fetch a node by a attribute and its value, regex is enabled for attribute value
        * 
        * @param string $attributeName name of the attribute that the tag contains
        * @param string $attributeValue value of the attribute, regex is enabled
        * @param string $content as reference
        * @param string $mode STRICT == $attributeValue, LIKE == %$attributeValue%, LLIKE == %$attributeValue, RLIKE == $attributeValue%
        * @return Elements
        */
        public static function GetByAttribute($attributeName, $attributeValue, &$content, $mode = "STRICT"){
            
            $pattern = str_replace("[[ATTRIBUTE_NAME]]",$attributeName,self::GetAttributePattern($mode));   
            $element = new Elements();
            $element->GetElements($attributeValue, $content, $pattern);
            
            return $element;          
        }
        /**
        * selects the pattern for the given mode 
        * @param string $mode STRICT, LIKE, LLIKE, RLIKE
        */
        private static function GetAttributePattern($mode){
            
            switch($mode){
                
                case 'STRICT': return self::PATTERN_BY_ATTRIBUTE_STRICT;  break;
                case 'LIKE':   return self::PATTERN_BY_ATTRIBUTE_LIKE;    break;
                case 'RLIKE':  return self::PATTERN_BY_ATTRIBUTE_RLIKE;   break;
                case 'LLIKE':  return self::PATTERN_BY_ATTRIBUTE_LLIKE;   break;
                
                default:       return self::PATTERN_BY_ATTRIBUTE_STRICT;  break; 
            }        
        }
        /**
        * return the element with the given iterator
        * @param int $iterator
        */
        public function GetElement($iterator){
            
            if(!isset($this->elements[$iterator])){
                throw new \Exception("element not exists, Elements::GetElement({$iterator});");    
            }
            return $this->elements[$iterator];   
        }
        /**
        * filters all found elements and compare its attribute to given values
        * @param string or array $attributeName
        * @param string or array $attributeValue
        * @return Elements
        */
        public function WhereAttribute($attributeName,$attributeValue){
   
            $new_elemets     = array();
            $attributeNames  = array();
            $attributeValues = array();
            
            if(is_array($attributeName)){
                $attributeNames = array_merge($attributeNames,$attributeName);    
            }
            else{
                $attributeNames[] = $attributeName;
            }
    
            if(is_array($attributeValue)){
                $attributeValues   = array_merge($attributeValues,$attributeValue);    
            }
            else{
                $attributeValues[] = $attributeValue;
            }
            
            foreach($attributeNames as $name){
                
                foreach($attributeValues as $value){
                    
                    self::Debug("filter elements where attribute({0})={1}",$name, $value);
                    
                    for($i = 0; $i < count($this->elements); $i++){
                 
                        if(isset($this->elements[$i]['attributes'][$name])){
   
                            if($this->elements[$i]['attributes'][$name] == $value){
                                $new_elemets[] = $this->elements[$i];     
                            }               
                        }
                    }     
                }                 
            }
            $this->elements = $new_elemets;
            return $this;
        }
        /**
        * Checks if there are any Element in class Storage
        * @return bool true else false
        */
        public function HasElements(){
            
            if($this->GetCount() > 0){
                return true;
            }
            return false;
        }
        /**
        * @return the count of found elements
        */
        public function GetCount(){
            return count($this->elements);
        }
        /**
        * fetch all tags in current content
        * @param string $content
        * @return Elements
        */
        public static function GetAllTags(&$content){
        
            $elements          = new Elements();
            $elements->content = &$content;
            
            $tags     = $elements->GetAllTagNames();
            $elements = self::GetByTagName($tags,$content);
            
            return $elements;
        }
        /**
        * @return array with a list of tagNames used in current content
        */
        private function GetAllTagNames(){
        
            preg_match_all("#\<([a-z0-9]+)[\s>]{1}#musx",$this->content,$match);  
            $match[1] = array_unique($match[1]);
              
            self::Debug("search all tags in document <pre>{0}</pre>",print_r(\Core\Tools\Escape::Object($match[1],0,0,1),1));
        
            return $match[1];
        }
        /**
        * @return array that contain all matched nodes
        */
        public function GetElementsArray(){
            return $this->elements;
        }
        /**
        * Get all Elements by Name
        * 
        * @param string $name string || array with tag names regex is enabled
        * @param string $content
        * @return Elements
        */
        public static function GetByName($name, &$content){
            
            self::Debug("get elements by name: {0}",print_r($name,1));
            $pattern = str_replace("[[ATTRIBUTE_NAME]]","name",Elements::PATTERN_BY_ATTRIBUTE_STRICT);
            
            $element = new Elements();
            $element->GetElements($name, $content, $pattern);
            
            return $element;
        }
        /**
        * Get all Elements by Tag Name
        * 
        * @param mixed $tagName string || array with tag names regex is enabled
        * @param string $content
        * @return Elements
        */
        public static function GetByTagName($tagName, &$content){

            $pattern = Elements::PATTERN_TAG_NAME;
            
            $element = new Elements();
            $element->GetElements($tagName, $content, $pattern);
            
            return $element;
        }        
        /**
        * Get all Elements by Id
        * 
        * @param string $id string || array, regex is enabled
        * @param string $content
        * @return Elements
        */
        public static function GetById($id, &$content){
            
            $pattern = str_replace("[[ATTRIBUTE_NAME]]","id",Elements::PATTERN_BY_ATTRIBUTE_STRICT);

            $element = new Elements();
            $element->GetElements($id, $content, $pattern);
            
            return $element;
        }
        /**
        * match the tag name in the given content
        * 
        * @param string $content
        * @return mixed tagName else bool false
        */
        private function GetTagName($content){
            
            if(preg_match("#^<(.*?[^\s>])[\s>]#mus",$content,$match)){
                self::Debug("tag name is: {0}",$match[1]); 
                
                return $match[1];      
            }
            else{
                self::Debug("unable to match tag name in {0}",print_r(\Core\Tools\Escape::Object($content,0,0,1),1));
                return false;
            }            
        }
        /**
        * checks if the given tag is a short tag like <br />
        * @param string $tag
        * @return bool
        */
        private function IsShortTag($tag){
            
            if(preg_match("#/>\$#",$tag)){
                return true;
            }
            return false;
        }
        /**
        * fetches all attributes in the given content
        * @param string $content
        */
        private static function FetchAttributes($content){
            
            $attributes = array();
            // matching all attributes
            preg_match_all("#([a-z]+)=\"(.*?)\"#mus", $content, $matches);
            
            for($j = 0; $j < count($matches[1]); $j++){
                
                $attributeName  = $matches[1][$j];
                $attributeValue = $matches[2][$j];
                
                $attributes[$attributeName] = $attributeValue;    
            }
            return $attributes;            
        }
        /**
        * matches the element and extract all attributes
        *         
        * @param string $name
        * @param string $content
        * @param string $pattern
        * @return Elements
        */
        private function GetElements($name,&$content, $pattern){
            
            $this->content = &$content;
            
            if(is_array($name)){
                
                self::Debug("GetElements by array");
                
                foreach($name as $subname){
                    $this->GetElements($subname, $content, $pattern);
                }
                return $this;    
            }
            // search the base tag
            $next_pattern = str_replace("[[NAME]]",$name, $pattern); 
            self::Debug("use pattern: {0}", \Core\Tools\Escape::Object($next_pattern,0,0,1,0));
            preg_match_all($next_pattern, $content, $elements, PREG_OFFSET_CAPTURE);
            
            self::Debug("match result: <pre>{0}</pre>", print_r(\Core\Tools\Escape::Object($elements,0,0,1,0),1)); 
             
            // search for all tags the a matching close tag
            for($i = 0;$i < count($elements[0]); $i++){
                
                self::Debug("<strong>start completing at offset {0}</strong>: <pre>{1}</pre>", $elements[0][$i][1], print_r(\Core\Tools\Escape::Object($elements[0][$i][0],0,0,1,0),1)); 
                
                $tagName    = $this->GetTagName($elements[0][$i][0]);
                $openingTag = $elements[0][$i][0];                    
                
                if($tagName === false){
                    continue;
                }
                // we don't want to overwrite old elements
                // taht the class fetched by array
                $iterator = count($this->elements);
                $iterator = $i + $iterator;
                
                // first setup of all information we later need
                $this->elements[$iterator]["text"]       = $openingTag;    
                $this->elements[$iterator]["openingTag"] = $openingTag;    
                $this->elements[$iterator]["closeTag"]   = "";    
                $this->elements[$iterator]["tagName"]    = $tagName;                
                $this->elements[$iterator]["offset"]     = $elements[0][$i][1];                
                $this->elements[$iterator]["isShortTag"] = false;
                $this->elements[$iterator]["innerHTML"]  = "";
                $this->elements[$iterator]["isXHTML"]    = true;
                $this->elements[$iterator]["valid"]      = true;
                $this->elements[$iterator]["attributes"] = self::FetchAttributes($openingTag);
                
                // short tag like <br />
                if($this->IsShortTag($openingTag)){
                    
                    $this->elements[$iterator]["isShortTag"] = true;
                    self::Debug("<span style=\"color: green;\">completed</span>: <pre>{0}</pre>", print_r(\Core\Tools\Escape::Object($openingTag,0,0,1,0),1)); 
                    continue; 
                }
                
                $this->elements[$iterator]["closeTag"] = "</{$tagName}>";
                $closeTag                              = preg_quote("{$this->elements[$iterator]["closeTag"]}","#");
                $rowCounter                            = 1;
                
                do{
                
                    $tag     = preg_quote($this->elements[$iterator]["text"],"#");
                    $pattern = "#{$tag}.*?{$closeTag}#mus";
                    
                    if(!preg_match($pattern, $this->content, $matches, PREG_OFFSET_CAPTURE, $elements[0][$i][1])){
                          
                        $this->elements[$iterator]["isShortTag"] = true;  
                        $this->elements[$iterator]["isXHTML"]    = false;
                        
                        // self::Debug("<span style=\"color: red;\">failed to match close tag</span>: <pre>{0}</pre>", print_r(\Core\Tools\Escape::Object($this->elements[$iterator]["text"],0,0,1,0),1));  
                        
                        $this->elements[$iterator]["valid"] = self::IsValidTag($this->elements[$iterator]["text"]);
                        
                        if(!$this->elements[$iterator]["valid"]){
                            throw new \Exception("invalid markup near: ".\Core\Tools\Escape::Object($this->elements[$iterator]["text"],1,250,1));    
                        }
                        break;    
                    }
                    self::Debug("part {0}: <pre>{1}</pre>",$rowCounter, print_r(\Core\Tools\Escape::Object($matches,0,0,1,0),1));
                    
                    $this->elements[$iterator]["text"] = $matches[0][0];
                    $rowCounter++;
                }
                while(self::CountOpenTags($tagName, $this->elements[$iterator]["text"]) != self::CountCloseTags($tagName, $this->elements[$iterator]["text"]));
                
                // extract the innerHTML
                $text = $this->elements[$iterator]["text"];
                
                // start behind the opening tag
                $text = mb_substr($text,  mb_strlen($this->elements[$iterator]["openingTag"]));
                // from beginning to start of the close tag
                $text = mb_substr($text,0,mb_strlen($text)-mb_strlen($this->elements[$iterator]["closeTag"]));
                
                $this->elements[$iterator]["innerHTML"] = $text;
                self::Debug("<span style=\"color: green;\">completed</span>: <pre>{0}</pre>", print_r(\Core\Tools\Escape::Object($this->elements[$iterator],0,0,1,0),1)); 
            }
                
            $uniqueNodes     = array();
            $uniqueNodes_md5 = array();
            
            foreach($this->elements as $key => $value){
            
                $md5 = md5($this->elements[$key]["text"]);    
                
                if(array_search($md5,$uniqueNodes_md5) === false){
                    $uniqueNodes[]     = $this->elements[$key];    
                    $uniqueNodes_md5[] = $md5;    
                }
            }
            $this->elements = $uniqueNodes;                

            return $this;               
        }
        /**
        * sets the innerHTML of a element
        * @param string $content
        * @param bool $replace_old the old content will be replaced, else if innerHTML is not empty it will be not replaced
        * @return Elements
        */
        public function SetInnerHTML($content, $replace_old = true){
            
            for($i = 0; $i < count($this->elements); $i++){ 
                
                if($replace_old == false && !empty($this->elements[$i]['innerHTML'])){
                    continue;            
                }
                $this->elements[$i]['isShortTag'] = false;
                $this->elements[$i]['innerHTML']  = $content;
            } 
            $this->UpdateContent(); 
            return $this;           
        }
        /**
        * deletes a node
        * @return Elements
        */
        public function DeleteNode(){
          
            for($i = 0; $i < count($this->elements); $i++){ 
                $this->content = str_replace($this->elements[$i]["text"],"",$this->content);   
            }
            return $this;          
        }
        /**
        * inserts the given content before all matched elements
        * 
        * @param string $content
        * @return Elements
        */
        public function InsertBefore($content){
        
            for($i = 0; $i < count($this->elements); $i++){    
                $this->content = str_replace($this->elements[$i]["text"], $content.$this->elements[$i]["text"],$this->content);   
            }
            return $this;
        }
        /**
        * inserts the given content after all matched elements
        * 
        * @param string $content
        * @return Elements
        */
        public function InsertAfter($content){
        
            for($i = 0; $i < count($this->elements); $i++){
                $this->content = str_replace($this->elements[$i]["text"], $this->elements[$i]["text"].$content,$this->content);   
            }
            return $this;
        }        
        /**
        * Replace the old attribute value with new one, 
        * if the attribute not exists new one is created
        * 
        * @param string $attribute
        * @param string $value
        */
        public function SetAttribute($attribute, $value){
      
            for($i = 0; $i < count($this->elements); $i++){
                
                self::Debug("set attribute({0})={1} where {2}",$attribute, $value, \Core\Tools\Escape::Object($this->elements[$i]["openingTag"],0,0,1));
                
                $this->elements[$i]['attributes'][$attribute] = $value; 
            }
            $this->UpdateContent();
            return $this;    
        }
        /**
        * Add the new attribute value to the old one
        * if the attribute not exists new one is created
        * 
        * @param string $attribute
        * @param string $value
        */        
        public function AddAttributeValue($attribute, $value){
   
             for($i = 0; $i < count($this->elements); $i++){
                 
                if(isset($this->elements[$i]['attributes'][$attribute])){
                    
                    $old_value = $this->elements[$i]['attributes'][$attribute];
                    $this->elements[$i]['attributes'][$attribute] = $old_value. " ". $value;  
                }   
                else{
                    $this->elements[$i]['attributes'][$attribute] = $value;   
                } 
                self::Debug("add attribute to {$this->elements[$i]['tagName']}: {$attribute}={$value}"); 
            }
            $this->UpdateContent(); 
            return $this;                      
        }
        /**
        * Update the Content and replace the old data with new one
        */
        private function UpdateContent(){
            
            self::Debug("parse elements for rebuild <pre>{0}</pre>",print_r(\Core\Tools\Escape::Object($this->elements,0,0,1),1));
            
            for($i = 0; $i < count($this->elements); $i++){    
            
                // concat all attributes of the tag
                $attributes = $this->RebuildAttributes($this->elements[$i]['attributes']);
                
                $old_tag   = $this->elements[$i]['text'];
                $tagName   = $this->elements[$i]['tagName'];
                
                // open the tag 
                $new_tag    = "<{$tagName} {$attributes}";
                
                // if we have innerHTML, we need a extra closing tag
                if(!$this->elements[$i]["isShortTag"]){
                    // first close the opening tag ;)
                    $new_tag .= ">". $this->elements[$i]['innerHTML']."</{$tagName}>";    
                }
                // opening tag is close tag
                else{
                    self::Debug("innerHTML is not set");
                    
                    if($this->elements[$i]["isXHTML"]){
                        $new_tag .= " />";      
                    }
                    else{
                        $new_tag .= ">";  
                    }  
                }
                
                if($new_tag != $old_tag){
                    $this->content = str_replace($old_tag,$new_tag,$this->content,$count);
                    $this->elements[$i]['text'] = $new_tag;   
                    
                    if(!$count){
                        self::Debug("unable to replace updated tag");    
                        self::Debug("old tag: \n" .\Core\Tools\Escape::Object($old_tag,0,0,1));    
                        self::Debug("new tag: \n" .\Core\Tools\Escape::Object($new_tag,0,0,1));    
                    }
                    else{
                        self::Debug("update tag");    
                        self::Debug("old tag: \n" .\Core\Tools\Escape::Object($old_tag,0,0,1));    
                        self::Debug("new tag: \n" .\Core\Tools\Escape::Object($new_tag,0,0,1));                          
                    }
                }
            }
            self::Debug("content update done");  
            $copy = $this->content;
            self::Debug("<pre>" .\Core\Tools\Escape::Object($copy,0,0,1)."</pre>");   
        }
        /**
        * Rebuild all attributes of a element
        * @param array $attributes
        */
        private function RebuildAttributes(array $attributes){
            
            $new_attributes = array();
            // iterate all attributes
            foreach($attributes as $attributeName => $attributeValue){
                $new_attributes[] = "{$attributeName}=\"{$attributeValue}\"";             
            }
            return implode(" ",$new_attributes);
        }
        /**
        * validate a tag by checking the count of open and close signs <>
        * @param string $tag
        * @return bool
        */
        private function IsValidTag($tag){
            
            self::Debug("validate: {0}", \Core\Tools\Escape::Object($tag,0,0,1));
            
            preg_match_all("#(<)#mus",$tag, $match);
            $open = count($match[1]);
            
            preg_match_all("#(>)#mus",$tag, $match);
            $close = count($match[1]);   
            
            self::Debug("validate end: \$open={0} , \$close={1}",$open,$close);
            
            if($open && $close){
                
                if($open == $close){
                    return true;    
                }
            }         
            return false;
        }
        /**
        * count the number of tags opened in the given content
        * @param string $content
        */
        private static function CountOpenTags($tagName, $content){
            preg_match_all("#(<{$tagName})#musx",$content,$match);   
            
            // self::Debug("count open results in <pre>{0}</pre>",print_r(\Core\Tools\Escape::Object($match,0,0,1),1));
            return count($match[1]);
        }
        /**
        * count the number of tags closed in the given content
        * @param string $content
        */        
        private static function CountCloseTags($tagName, $content){
            preg_match_all("#(</{$tagName}>)#musx",$content,$match);   
            
            // self::Debug("count close results in <pre>{0}</pre>",print_r(\Core\Tools\Escape::Object($match,0,0,1),1));
            return count($match[1]);
        }
        //--------------------------------------------------------------------
        // Specific tag functions
        //--------------------------------------------------------------------
        /**
        * Selects one or more values of a select tag
        * @param mixed $selectedValue string or array of values
        * @param string &$content
        */
        public function SetSelectedValue($selectedValue){
        
            // fetch the select tag(s) and its content
            $elements = $this->GetElementsArray();
            
            foreach($elements as $element){
                
                // copy of old version for later replacment with the new version in the content string
                $old_node = $element["text"];
                // fetching the options in the text node of the select tag
                // filter all options that have the given value(s) and that them as selected
                Elements::GetByTagName("option",$element["text"])->WhereAttribute("value",$selectedValue)->SetAttribute("selected","selected");
                // last but not least, replace the old select text node with the new one
                $this->content = str_replace($old_node,$element["text"], $this->content);
                
                if(self::DEBUG_MODE){
                    
                    self::Debug("replace old node\n<pre>{0}</pre>",\Core\Tools\Escape::Object($old_node,       0,0,1));
                    self::Debug("with new node\n<pre>{0}</pre>",   \Core\Tools\Escape::Object($element["text"],0,0,1));
                }
            }   
            return $this;       
        }     
        /**
        * small debug function to print some stuff
        * @param string $message
        * @parameter p
        */
        private static function Debug($message){
            
            if(func_num_args() > 1){
                $args = func_get_args();
                
                for($i = 0; $i < func_num_args()-1; $i++){
                    $message = str_replace("{{$i}}",$args[$i+1],$message);    
                }
            }
            if(!Elements::DEBUG_MODE){
                return;
            }
            $output = "<span style=\"\">".date("m:s")."> ".$message."<br /></span>"; 
            print $output;   
        }       
    }
}  
?>