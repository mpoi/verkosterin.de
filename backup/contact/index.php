<?php
include("config.system.php");
include("config.fields.php");

// all file paths
$formTplPath           = REAL_PATH."template/form.html";
$formSendTplPath       = REAL_PATH."template/form.send.html";
$emailAdminHtmlTplPath = REAL_PATH."template/email.admin.html"; 
$emailUserHtmlTplPath  = REAL_PATH."template/email.user.html"; 

// html id of the contact form id
$formId = "contact_form";
// programmcode start -----------------------------------------

try{
    $tpl = new \Core\Tools\Tpl($formTplPath);
    
    if(isset($_POST["submit"])){
        
        $Bridge = new \Core\Forms\Bridge($tpl, "contact_form");
        
        foreach($mandatoryFields as $name => $errorMsg){
            
            switch($name){
                
                // special cases like email, captcha, ... validation
                case 'email': 
                    if(!isset($_POST[$name]) || empty($_POST[$name]) || !\Core\Tools\Functions::IsValidEmail($_POST['email'])){
                        $Bridge->AddError($name, $errorMsg);    
                    }                
                
                break;
                case 'captcha': 
                    if(@$_SESSION['captcha'] != @$_POST['captcha']){
                        $Bridge->AddError($name, $errorMsg);    
                    }                
                break;
                
                default: 
                    if(!isset($_POST[$name]) || empty($_POST[$name])){
                        $Bridge->AddError($name, $errorMsg);    
                    }           
            }
        }
        
        // attachments validation
        if(isset($_FILES) && count($_FILES) > 0){
         
            foreach($_FILES as $name => $unused){
                
                if(empty($_FILES[$name]["name"])){
                    continue;
                }
                
                if(\Core\Tools\Functions::ConvertUnitSize("b","k",$_FILES[$name]["size"]) > $uploadMaxSize){
                    $Bridge->AddError($name, $uploadMaxSizeErr);    
                }
                elseif(array_search(\Core\Tools\Functions::GetFileExtension($_FILES[$name]["name"]),$uploadValidExtensions) === false){
                    $Bridge->AddError($name, $uploadExtensionErr);      
                }
            }  
        }
        
        // some user erros
        if($Bridge->HasErrors()){
        
            \Core\Tools\Escape::Object($_POST,0,0,1,0);
            $Bridge->ArrayToForm($_POST);
            
            if($insertCSSClassToInputs){
                $Bridge->MarkInputByError();      
            }
            
            if($directHelp){
                $Bridge->PlaceErrorMSGByInput();    
            }
            
            if($groupedErrorMessage){
                $systemMessage = $Bridge->ErrorToString("<br />");
                $tpl->InsertSystemMessage($systemMessage);             
            }
        }
        // all done
        else{
            
            $emailAdminTpl = new \Core\Tools\Tpl($emailAdminHtmlTplPath);
            $emailUserTpl  = new \Core\Tools\Tpl($emailUserHtmlTplPath);
            
            foreach($_POST as $key => $value){
                
                $value = \Core\Tools\Escape::Object($value,1,0,1,0);
                $value = nl2br($value);
                
                $emailAdminTpl->Replace($key, $value);    
                $emailUserTpl->Replace( $key, $value);    
            }   
            
            $mailer = new \Core\Email\Mailer();
            $mailer->SetMailMode(MAIL_MODE);
            
            $email  = new \Core\Email\Mail(ADMIN_EMAIL, $_POST['email'], $_POST['name'], $_POST['email'], $subjectAdminEmail, $emailAdminTpl->Render(true), "", CHARSET); 
            
            if(isset($_FILES) && count($_FILES) > 0){
             
                foreach($_FILES as $name => $unused){
                    
                    if(empty($_FILES[$name]["name"])){
                        continue;
                    }
                    $email->AppendAttachment($_FILES[$name]["name"],file_get_contents($_FILES[$name]["tmp_name"]));
                }
            }            
            
            $mailer->Send($email); 
            
            if(isset($_POST['copy'])){
                $email = new \Core\Email\Mail($_POST['email'], BLIND_EMAIL, "", BLIND_EMAIL, $subjectUserEmail, $emailUserTpl->Render(true), "", CHARSET);     
                $mailer->Send($email);
            }
            
            $tpl = new \Core\Tools\Tpl($formSendTplPath);           
        }
    }  
}
catch(\Exception $e){
    $tpl->InsertSystemMessage($e->getMessage());
    
    \Core\Tools\Escape::Object($_POST,0,0,1,0);
    $Bridge->ArrayToForm($_POST);    
}
print $tpl->Render(true);  
?>