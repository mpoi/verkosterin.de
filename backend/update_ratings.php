<?php
	include '../header.php';
	include '../banner.php';

	if($_POST['ratingid'])
	{
		echo "TEST";
		updateRatings($_POST['ratingid']);
	}
			
	
?>