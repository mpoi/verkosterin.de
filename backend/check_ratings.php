<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<?php include '../header.php' ?>
	<meta name='robots' content='noindex' />
	
</head>

<body>
		
		<?php include '../banner.php' ?>
	
		<div id="menu_container">
			<a href="../index.php">Startseite</a>
			<a class="rights" href="check_ratings.php">Bewertungen</a>
			<a class="rights" href="locations.php">Verkostungsorte</a>
			<a class="rights" href="visitorcodes.php">Kunden-Codes</a>
		</div>

		<div id="main_container">

			<div class="main_left_start"></div>
			<div class="main_content">
				<h1>Bewertungen freigeben</h1>
				
				<?php
				
					$result = getReservedRatings();
					
					echo "<table id='rating_tab'>";
					
					echo "<tr><td>ID</td><td>Sterne</td><td>Bewertung</td></tr>";
					
					while($row = mysqli_fetch_array($result))
					{
						$val=$row['id'];
 					  echo "<form action='update_ratings.php' method='POST'><tr><td><input type='text' name='ratingid' maxlength='10' value='$val' style='width:30px;'></td><td>" . $row['stars'] . "</td><td width='500px'>" . $row['text'] . "</td><td><input type='submit' value='freigeben' /></td></tr></form>";
					}
					
					echo "</table>";
				
				?>

			</div>
			
		</div>
		
</body>
</html>