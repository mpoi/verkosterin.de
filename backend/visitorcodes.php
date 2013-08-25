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
				<h1>Visitenkarten-Codes generieren</h1>
				<div style="margin-bottom:25px;">
			
				<?php
				
					$result = getVisitorCodes();
					
					echo "<table id='visitor_tab'>";
					
					echo "<tr><td>ID</td><td>Code</td><td>Bewertung</td></tr>";
					
					while($row = mysqli_fetch_array($result))
					{
					  $col='red';
					  $ratingstat = 'abgegeben';
					  
					  if($row['used'] == 0)
					  {
					  	$col = 'green';
					  	$ratingstat = 'ausstehend';
					  }
					  
					  echo "<tr style='background-color:$col;'><td>" . $row['id'] . "</td><td>" . $row['code'] . "</td><td>" . $ratingstat . "</td></tr>";
					}
					
					echo "</table>";
				
				?>
				
				</div>
				
				
				<form action="insertvcodes.php" method="POST">
						
					<input type="submit" value="neuen Code anlegen" />
						
				</form>
				
			</div>
			
		</div>
		
</body>
</html>