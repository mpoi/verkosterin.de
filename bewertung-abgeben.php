<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<?php include 'header.php' ?>

</head>

<body>
		
		<?php include 'banner.php' ?>
	
		<div id="menu_container">
			<a href="#">Startseite</a>
			<a href="#">Gisela Hiermer</a>
			<a href="#">Eindr&uuml;cke</a>
			<a href="#">Kontakt/ Buchen</a>
			<a class="rights" href="#">Bewertungen</a>
			<a class="rights" href="#">Bewertung abgeben</a>
		</div>

		<div id="main_container">

			<div class="main_left_start"></div>
			<div class="main_content">
				<h1>Bewertung abgeben</h1>
				<form action="insertrating.php" method="POST">
				
					<table style="margin:auto;">
						<tr>
							<td>Wie war ich?</td>
							<td>
								<input type="radio" name="stars" value="5"> sehr gut
								<input type="radio" name="stars" value="4" checked="1"> gut
								<input type="radio" name="stars" value="3"> geht so
								<input type="radio" name="stars" value="2"> weniger gut
								<input type="radio" name="stars" value="1"> schwach
							</td>
						</tr>
						<tr>
							<td valign="top">Ihr Name:</td>
							<td><div><input type="text" name="name" maxlength="250" /></div></td>
						</tr>
						<tr>
							<td valign="top">Ihre EMail-Adresse:</td>
							<td><div><input type="text" name="email" maxlength="250" /></div></td>
						</tr>
						<tr>
							<td valign="top">Ihre Bemerkung:</td>
							<td><div>
								<textarea name="text" rows="10" cols="50" style="resize:none;"></textarea>
							</div></td>
						</tr>
						<tr>
							<td valign="top">Visitenkarten-Code:</td>
							<td><div><input type="text" name="auth" maxlength="10" />(falls vorhanden)</div></td>
						</tr>
						<tr>
							<td></td>
							<td align="center"><input type="submit" value="Bewertung abgeben" /></td>
						</tr>
					</table>
					
				</form>
			</div>
			
		</div>
		
</body>
</html>