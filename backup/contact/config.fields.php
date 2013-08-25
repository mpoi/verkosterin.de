<?php
/**
* mandatory fields config
* 
* if your html input, select, textarea, ... has as name="firstname"
* 
* firstname => "your message here"
* 
* define a error message in the array below,
* don't forget the comma at the end of line,
* only the last line is without a comma
*/
$mandatoryFields = array(
    "captcha"   => "Security question was answered incorrectly",
    "email"     => "Invalid e-mail address",
    "name"      => "Please state your name",
    "message"   => "Enter a message"
);
// Subject to the admin
$subjectAdminEmail = "Somebody wrote a message";
// Subject to the user
$subjectUserEmail  = "Copy of your message";
/**
* Configuration of attachments (fileuploads)
*/
$uploadMaxSize    = 4096; // max size of each upload in kb
$uploadMaxSizeErr = "Maximum file size has been exceeded"; 

// valid filetyp extensions
$uploadValidExtensions = array(".jpg",".pdf",".gif");
$uploadExtensionErr    = "Invalid file type";

// configuration of error message layout
// ---------------------------------------------
// insertes into the class attribute of an input
// the css class input_error_marker
$insertCSSClassToInputs = true; 

// inserts a div with css class after each input that
// has errors, you can style the div with css
$directHelp = true;

// Inserts a grouped error message into the system_message div a the top
$groupedErrorMessage = false;
?>