<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>iframe</title>
<?php echo $head?>
<script language="javascript" type="text/javascript">
function autofit_iframe(id){
 if(document.getElementById) {
   parent.document.getElementById(id).style.height = "150px";
	 parent.document.getElementById(id).style.height = this.document.body.scrollHeight+"px"
 }
}
</script>
</head>

<body bgcolor="#F9F9F9" onload="<?php echo $onload?>">
<?php echo $content?>

</body>
</html>