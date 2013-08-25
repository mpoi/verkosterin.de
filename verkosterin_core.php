<?php

function generateRandomString($length = 4) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function connectToDatabase() {
	$dbv_host='localhost';
	$dbv_user='gisa';
	$dbv_pw='300966?';
	$dbv_name='wsb_verkosterin';
	
	$con=mysqli_connect($dbv_host, $dbv_user, $dbv_pw, $dbv_name);

	if (mysqli_connect_errno())
	{
	  echo "<div class='errormsg'>Failed to connect to MySQL: " . mysqli_connect_error() . "</div>";
	  return null;
	}
	
	mysqli_set_charset($con, 'utf8');
	
	return $con;
}

function disconnectDatabase($con) {
	mysqli_close($con);
}

function getVisitorCodes() {
	$con = connectToDatabase();
			
	if (mysqli_connect_errno())
	{
	  echo "<div class='errormsg'>Failed to connect to MySQL: " . mysqli_connect_error() . "</div>";	  
	  disconnectDatabase($con);
	  return null;
	}
	
	$result = mysqli_query($con,"SELECT *, (SELECT COUNT(*) FROM ratings WHERE authcode=code) AS used FROM visitor_codes ORDER BY id ASC");
	
	disconnectDatabase($con);
	
	return $result;
}

function getNext3Locations() {
	$con = connectToDatabase();
			
	if ($con == null)
	{
	  return null;
	}
	
	$result = mysqli_query($con,"SELECT * FROM locations WHERE locdate >= CURDATE() ORDER BY locdate ASC LIMIT 3");
	
	disconnectDatabase($con);

	return $result;
}

function getRatings() {
	$con = connectToDatabase();
			
	if ($con == null)
	{
	  return null;
	}
	
	$result = mysqli_query($con,"SELECT * FROM ratings WHERE auth=1 OR auth=2 ORDER BY ratingdate DESC");
	
	disconnectDatabase($con);

	return $result;
}

function getRatingsAvg() {
	$con = connectToDatabase();
	$getRatingsAvgCount=0;
	
	if ($con == null)
	{
	  return null;
	}
	
	$getRatingsAvgResult = mysqli_query($con,"SELECT ROUND(AVG(stars)) FROM ratings WHERE auth>0;");
	
	while($rowb = mysqli_fetch_array($getRatingsAvgResult))
	{
		$getRatingsAvgCount=$rowb['ROUND(AVG(stars))'];
	}
	
	disconnectDatabase($con);
	
	return $getRatingsAvgCount;
}

function getReservedRatings() {
	$con = connectToDatabase();
			
	if ($con == null)
	{
	  return null;
	}
	
	$result = mysqli_query($con,"SELECT * FROM ratings WHERE auth=0 ORDER BY ratingdate ASC");
	
	disconnectDatabase($con);

	return $result;
}

function updateRatings($id) {
	$con = connectToDatabase();
			
	if ($con == null)
	{
	  return null;
	}
	
	$sql="UPDATE ratings SET auth=2 WHERE id=$id";
	
	if (!mysqli_query($con,$sql))
	{
	  die("<div class='errormsg'>Error: " . mysqli_error($con));
	}
	
	disconnectDatabase($con);
	
	return 0;
}

function setRating($stars,$name,$email,$auth,$text) {
	$con = connectToDatabase();
	
	$disablechars = array("'", "\"");
	$name = str_replace($disablechars, "", $name);
	$email = str_replace($disablechars, "", $email);
	$auth = str_replace($disablechars, "", $auth);
	$text = str_replace($disablechars, "", $text);
			
	if ($con == null)
	{
	  return null;
	}

	$result = mysqli_query($con,"SELECT COUNT(*) FROM visitor_codes WHERE code='$auth' AND useddate IS NULL");
	$count = mysql_result($result,0); 
		
	if($count==1)
	{
		$sql = "INSERT INTO ratings (stars,name,text,email,authcode,auth) VALUES ($stars,'$name','$text','$email','$auth',1)";

		if (!mysqli_query($con,$sql))
		{
		  die("<div class='errormsg'>Error: " . mysqli_error($con));
		}
		else
		{
			$sql = "UPDATE visitor_codes SET useddate=now() WHERE code='$auth' AND useddate IS NULL";

			if (!mysqli_query($con,$sql))
			{
			  die("<div class='errormsg'>Error: " . mysqli_error($con));
			}
		}
	}
	else
	{
		$sql = "INSERT INTO ratings (stars,name,text,email,auth) VALUES ($stars,'$name','$text','$email',0)";

		if (!mysqli_query($con,$sql))
		{
		  die("<div class='errormsg'>Error: " . mysqli_error($con));
		}
	}
	
	disconnectDatabase($con);
}

function setRating2($name,$email,$tel,$text) {
	$disablechars = array("'", "\"");
	$name = str_replace($disablechars, "", $name);
	$email = str_replace($disablechars, "", $email);
	$tel = str_replace($disablechars, "", $tel);
	$text = str_replace($disablechars, "", $text);
			
	$from = "$name <$email>";
	$to = "Marco Pointinger <marco.pointinger@me.com>";
	$subject = "Kontaktanfrage verkosterin.de";
	$body = $text;
	
	$host = "mail.verkosterin.de";
	$username = "gisa@verkosterin.de";
	$password = "300966";
	
	$headers = array ('From' => $from,
					  'To' => $to,
					  'Subject' => $subject);
					  
	$smtp = Mail::factory('smtp',
						  array ('host' => $host,
						  		 'auth' => true,
						  		 'username' => $username,
						  		 'password' => $password));
	
	$mail = $smtp->send($to, $headers, $body);
	
	if (PEAR::isError($mail)) {
		echo("<p>" . $mail->getMessage() . "</p>");
	} else {
		echo("<p>Message successfully sent!</p>");
	}
}

function setVisitorCode() {
	$con = connectToDatabase();
			
	if ($con == null)
	{
	  return null;
	}
	
	$genCode = generateRandomString();
	$sql = "INSERT INTO visitor_codes (code) VALUES ('$genCode')";

	if (!mysqli_query($con,$sql))
	{
	  die("<div class='errormsg'>Error: " . mysqli_error($con));
	}
	
	disconnectDatabase($con);
}

function setLocation() {
	$con = connectToDatabase();
			
	if ($con == null)
	{
	  return null;
	}

	$time = strtotime($_POST[locdate]);
	$newformat = date('Y-m-d',$time);
	$sql = "INSERT INTO locations (location, locdate) VALUES ('$_POST[location]', '$newformat')";

	if (!mysqli_query($con,$sql))
	{
	  die("<div class='errormsg'>Error: " . mysqli_error($con));
	}
	
	disconnectDatabase($con);
}

?>