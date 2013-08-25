<?php
	include 'header.php';
	include 'verkosterin_core.php';
	
	if($_POST['stars'] && $_POST['name'] <> '' && $_POST['email'] <> '')
		setRating($_POST['stars'],$_POST['name'],$_POST['email'],$_POST['auth'],$_POST['text']);
	
	echo "Bewertung abgegeben. Danke. <a href='www.verkosterin.de'>zur&uuml;ck zu verkosterin.de</a>";

?>