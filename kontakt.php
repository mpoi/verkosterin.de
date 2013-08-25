<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<?php 
		include 'header.php';
	    session_start();
	?>
	
	<title></title>
</head>
<body>

		<?php 
			include 'banner.php';
			include 'menu.php';
		?>
		

		<div id="main_container">

			<div class="main_left_start"></div>
			<div class="main_content">
				<h1>Kontakt/ Buchen</h1>
				<form action="send_contact.php" method="POST">
				
					<table style="margin:auto;">
						<tr>
							<td>Ihr Name:</td>
							<td><div><input type="text" name="name" maxlength="250" /></div></td>
						</tr>
						<tr>
							<td valign="top">Ihre E-Mail-Adresse:</td>
							<td><div><input type="text" name="email" maxlength="25" /></div></td>
						</tr>
						<tr>
							<td valign="top">Ihre Telefonnummer:</td>
							<td><div><input type="text" name="tel" maxlength="25" /></div></td>
						</tr>
						<tr>
							<td valign="top">Ihre Anfrage:</td>
							<textarea name="text" rows="10" cols="50" style="resize:none;"></textarea>
						</tr>
						<tr>
							<td></td>
							<td align="center"><input type="submit" value="absenden" /></td>
						</tr>
						
					</table>
					
				</form>
			</div>
			
		</div>
	

</body>
</html>