<html>
<head>
<title>The Yorker</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/stylesheets/general.css" rel="stylesheet" type="text/css">
<link href="/stylesheets/stylesheet.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#CED8D9" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preloader(); if(typeof onLoad == 'function') onLoad();">

<script src="/javascript/jumpto.js" type="text/javascript"></script>

<script language="JavaScript">

function preloader()
{
     // counter
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

  <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
  <tr>
		<td align="middle" valign="center">


<table id="Table_01" width="780" height="581" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
	<tr>
		<td height="22" style="padding-right:10px;" align="right" class="HeaderMenu">
			<a class="HeaderLinks" href="<?php echo site_url('/prototype/sframe/general/home')?>">home</a> | 
			<a class="HeaderLinks" href="<?php echo site_url('/prototype/sframe/general/logon')?>">log in</a> | 
			<a class="HeaderLinks" href="http://yorkipedia.theyorker.co.uk/">yorkipedia</a> | 
			<a class="HeaderLinks" href="#">contact us</a> | 
			<a class="HeaderLinks" href="#">about</a> | 
			<span style="color: #2DC6D7;">
			search <input type="text" style="font-size:10px; height: 19px; border-style:solid; border-color:#2DC6D7; border-width: 2px;">
			</span>
		</td>
	</tr>
	<tr>
		<td bgcolor="#ffffff" height="108" >
			<table id="Table_01" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="background-image:url(/images/prototype/header/homepage_bk.gif); background-repeat:repeat-x; height:108;">
				<tr>
					<td>
						<img src="/images/prototype/header/header_Layer-1.gif" width="275" height="108" alt="" border="0"></td>
					<td>
						<a href="<?php echo site_url('/prototype/sframe/news/news_main')?>" onMouseOut="document.img01.src='/images/prototype/header/header_Layer-4.gif';" onMouseOver="document.img01.src='/images/prototype/header/header2_Layer-4.gif';">
						<img name="img01" src="/images/prototype/header/header_Layer-4.gif" width="107" height="108" alt="News" border="0" ></a></td>
					<td style="width: 40;">
						</td>
					<td>
						<a href="<?php echo site_url('/prototype/sframe/listings/listings_main')?>" onMouseOut="document.img02.src='/images/prototype/header/header_Layer-3.gif';" onMouseOver="document.img02.src='/images/prototype/header/header2_Layer-3.gif';">
						<img name="img02" src="/images/prototype/header/header_Layer-3.gif" width="107" height="108" alt="Listings" border="0" ></a></td>
					<td style="width: 33;">
						</td>
					<td>
						<a href="<?php echo site_url('/prototype/sframe/reviews/reviews_main')?>" onMouseOut="document.img03.src='/images/prototype/header/header_Layer-2.gif';" onMouseOver="document.img03.src='/images/prototype/header/header2_Layer-2.gif';">
						<img name="img03" src="/images/prototype/header/header_Layer-2.gif" width="108" height="108" alt="Reviews" border="0" ></a></td>
					<td>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor="#ffffff">
		<td width="750" valign="top">
		<?php $this->load->view($content_view,$subdata); ?>
		</td>
	</tr>
</table>
<small>Page rendered in {elapsed_time} seconds</small>


  </td>
  </tr>
  </table>

<!-- Start of StatCounter Code -->
<script type="text/javascript" language="javascript">
var sc_project=1998064; 
var sc_invisible=1; 
var sc_partition=18; 
var sc_security="7146b8cd"; 
</script>

<script type="text/javascript" language="javascript" src="http://www.statcounter.com/counter/counter.js"></script><noscript><a href="http://www.statcounter.com/" target="_blank"><img  src="http://c19.statcounter.com/counter.php?sc_project=1998064&amp;java=0&amp;security=7146b8cd&amp;invisible=1" alt="free web site hit counter" border="0"></a> </noscript>
<!-- End of StatCounter Code -->

<!-- Start of Google Analytics -->
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-864229-1";
urchinTracker();
</script>
<!-- End of Google Analytics -->

</body>
</html>