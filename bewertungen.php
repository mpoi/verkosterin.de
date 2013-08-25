<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<?php include 'header.php' ?>
	
	<script type='text/javascript'>
	
		jQuery(document).ready(function(){

			  var $container = $('#mc1');

				$container.masonry({
				  
				  itemSelector: '.rating_entry'
				});
			
		});
	</script>
	
	
	
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
				<h1>Bewertungen</h1>
				
				<?php 
					$result = getRatings();
					
					echo "<div id='mc1'>";
					
						while($row = mysqli_fetch_array($result))
						{
						  $type='Kunde';
						  $css_class='rating_entry_customer';
						  $starcss='rating_stars-'.$row['stars'];
						  
						  if($row['auth'] == 1)
						  {
						  	$type = 'Händler';
						  	$css_class='rating_entry_business';
						  }
						  
						  echo "<div class='rating_entry ".$css_class."'><span class='".$starcss."'></span><span class='rating_name'>".$row['name']."</span><span class='rating_type'>(".$type.")</span></br><span class='rating_text'>".$row['text']."</span></div>";
						}
					
					echo "</div>";
				
				?>
				
			</div>
			
		</div>
	

</body>
</html>