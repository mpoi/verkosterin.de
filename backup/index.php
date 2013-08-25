<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<?php include 'header.php' ?>
	<?php session_start(); ?>
	<link rel="stylesheet" type="text/css" href="contact/css/style.css">
  <title>Verkosterin</title>
  
  <script type="text/javascript">
  
  $(document).ready(function() {

	 $("#one").liteAccordion({
		 containerWidth : 1200,
            containerHeight: 501,
            theme : 'dark',
            headerWidth: 74
	 });
	});
	
	</script>
  
</head>
<body>
	<div id="main">
	
		<div id="contentimage"><img src="/images/verkosterin-logo.png" title="Verkosterin" width="350px" /></div>

		<div id="content" style="position:relative;margin-top:-70px;">
			<div id="contenttext">

		        <div id="one">
		            <ol>
		                <li>
		                    <h2><span class="home"></span></h2>
		                    <div style="background-color:#7ebf19;">
		                        <div style="padding-left:20px;padding-top:50px;">
		                        	<h3>TEST</h3>
		                        </div>
		                    </div>
		                </li>
		                <li>
		                    <h2><span class="cal"></span></h2>
		                    <div style="background-color:#7ebf19;">
		                        <div style="padding-left:20px;padding-top:50px;">
		                        	<h3>TEST</h3>
		                        </div>
		                    </div>
		                </li>
		                <li>
		                    <h2><span class="gal"></span></h2>
		                    <div style="background-color:#7ebf19;">
		                        <div style="padding-left:20px;padding-top:50px;">
		                        	<h3>TEST</h3>
		                        </div>
		                    </div>
		                </li>
		                <li>
		                    <h2><span class="bak"></span></h2>
		                    <div style="background-color:#7ebf19;">
		                        <div style="padding-left:20px;padding-top:50px;">
		                        	<?php include 'contact/template/form.html' ?>
		                        </div>
		                    </div>
		                </li>
		                <li>
		                    <h2><span class="bew"></span></h2>
		                    <div style="background-color:#7ebf19;">
		                        <div style="padding-left:20px;padding-top:50px;">
		                        	<h3>TEST</h3>
		                        </div>
		                    </div>
		                </li>
		            </ol>
		            <noscript>
		                <p>Please enable JavaScript to get the full experience.</p>
		            </noscript>
		        </div>
			</div>

<div id="footer" style="width:100%;text-align:right;">Impressum</div>

 		</div>
		
		
		
	</div>
	
</body>
</html>