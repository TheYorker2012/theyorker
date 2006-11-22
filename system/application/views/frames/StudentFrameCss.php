<html>
<head>
<title>The Yorker</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/stylesheets/general.css" rel="stylesheet" type="text/css">
<link href="/stylesheets/StudentFrame.css" rel="stylesheet" type="text/css">
</head>

<body onLoad="preloader()">
<script language="JavaScript">
	function preloader()
	{
		// counter (Isnt "i" illegal?)
		var i = 0;

		// create object
		imageObj = new Image();

		// set image list
		images = new Array();
		images[0]="/images/prototype/header/header2_Layer-4.gif";
		images[1]="/images/prototype/header/header2_Layer-3.gif";
		images[2]="/images/prototype/header/header2_Layer-2.gif";

		// start preloading
		for(i=0; i<=3; i++)
		{
			imageObj.src=images[i];
		}
	}
</script>

<div class="WholePage">
	<div class="HeaderMenu">
		<a class="HeaderLinks" href="#">home</a> | 
		<a class="HeaderLinks" href="#">log in</a> | 
		<a class="HeaderLinks" href="#">yorkipedia</a> | 
		<a class="HeaderLinks" href="#">contact us</a> | 
		<a class="HeaderLinks" href="#">about</a> | 
		<span class="HeaderSearch">search <input type="text" class="HeaderSearchBox"></span>
	</div>
	<div class="HeaderBanner">
		<span style="float: left;"><img name="titleimg" src="http://localhost/images/prototype/header/header_Layer-1.gif"></span>
		<a href="#"><img name="img01" src="/images/prototype/header/header_Layer-4.gif" width="107" height="108" alt="News" border="0" onMouseOut="document.img01.src='/images/prototype/header/header_Layer-4.gif';" onMouseOver="document.img01.src='/images/prototype/header/header2_Layer-4.gif';"></a>
		<a href="#"><img name="img02" src="/images/prototype/header/header_Layer-3.gif" width="107" height="108" alt="Listings" border="0" onMouseOut="document.img02.src='/images/prototype/header/header_Layer-3.gif';" onMouseOver="document.img02.src='/images/prototype/header/header2_Layer-3.gif';"></a>
		<a href="#"><img name="img03" src="/images/prototype/header/header_Layer-2.gif" width="108" height="108" alt="Reviews" border="0" onMouseOut="document.img03.src='/images/prototype/header/header_Layer-2.gif';" onMouseOver="document.img03.src='/images/prototype/header/header2_Layer-2.gif';"></a>
	</div>
	<div class="SubView">
<?php $this->load->view($content_view,$subdata); ?>

	</div>
</div>
<div class="footer">
	<small>Page rendered in {elapsed_time} seconds</small>
</div>
</body>
</html>