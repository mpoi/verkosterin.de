<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<?php include '../header.php' ?>
	<meta name='robots' content='noindex' />
	
	<script>
	  $(function() {
	    $( "#datepicker" ).datepicker();
	  });
	</script>
	
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	
</head>

<body>
		
		<?php include '../banner.php' ?>
	
		<div id="menu_container">
			<a href="../index.php">Startseite</a>
			<a class="rights" href="locations.php">Verkostungsorte</a>
			<a class="rights" href="visitorcodes.php">Kunden-Codes</a>
		</div>

		<div id="main_container">

			<div class="main_left_start"></div>
			<div class="main_content">
				<h1>Verkostungsort eintragen</h1>
				<form action="insertloc.php" method="POST">
				
					<table style="margin:auto;">
						<tr>
							<td>Ort:</td>
							<td><div><input type="text" name="location" maxlength="250" /></div></td>
						</tr>
						<tr>
							<td valign="top">Datum:</td>
							<td><div><input type="text" id="datepicker" name="locdate" maxlength="25" /></div></td>
						</tr>
						<tr>
							<td></td>
							<td align="center"><input type="submit" value="Eintrag anlegen" /></td>
						</tr>
					</table>
					
				</form>
			</div>
			
		</div>
		
</body>
</html>