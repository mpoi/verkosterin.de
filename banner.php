<div id="head">

	<a href="www.verkosterin.de" title="verkosterin.de">
	
		<img src="/images/logo-70.png" alt="" style="position:relative;left:400px;top:20px;width:200px;z-index:2;"/>
	
		<div id="head_container">
			<span id="headline">Verkosterin</span><br/>
			<span id="sub_headline">Gisela Hiermer | Obertraubling</span>
		</div>
	</a>
	
	<?php
		
		include 'verkosterin_core.php';
		$result = getNext3Locations();
		$i=1;
		
		$heute = date("d.m.Y".time()); 
		$morgen = strtotime("+1 day"); 
		$morgenist = date("d.m.Y", $morgen);

		while($row = mysqli_fetch_array($result))
		{
			$myDate = date("d.m.Y", strToTime($row['locdate']));
			$myDateText = $myDate;
			
			if($myDate<=$morgenist)
				$myDateText='morgen';
			
			if($myDate<=$heute)
				$myDateText='heute';
			
			echo "<div class='date".$i."'><span>" . $myDateText . ": " . $row['location'] . "</span></div>";
			$i++;
		}
	
		$avg_rating = getRatingsAvg();
		
		echo "<div class='avg_rate star-$avg_rating'></div>";

	?>

</div>