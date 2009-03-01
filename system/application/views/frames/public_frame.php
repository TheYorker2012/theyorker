<?php
// Must echo through PHP in case short tags is turned on
echo('<?xml version="1.0" encoding="UTF-8"?>');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="description" content="<?php echo(xml_escape($description)); ?>" />
	<meta name="keywords" content="<?php echo(xml_escape($keywords)); ?>" />
	<meta name="verify-v1" content="slrlMuizkqTRTqt5W2zF1EZ6nMrwx+/qRmNDJ7xt2m8=" />
<?php if (isset($head_title)) { ?>
	<meta name="title" content="<?php echo(xml_escape($head_title)); ?> - The Yorker" />
<?php }
if (isset($main_image)) { ?>
	<link rel="image_src" href="http://<?php echo($_SERVER['SERVER_NAME'].$main_image); ?>" />
<?php }
/* Valid values for medium_type are "audio", "image", "video", "news", "blog" and "mult" */
if (isset($medium_type)) { ?>
	<meta name="medium" content="<?php echo($medium_type); ?>" />
<?php } ?>

	<title><?php
		// FIXME: backwards compatibility, remove when all pages are shown with titles
		if(isset($head_title)) {
			echo(xml_escape($head_title));
		} else {
			echo('no pagename');
		}
	?> - The Yorker</title>

	<link rel="shortcut icon" href="/images/favicon.png" />
	<link rel="alternate" type="application/rss+xml" title="The Yorker - Campus News" href="/feeds/news" />

	<link href="/stylesheets/v2.css" rel="stylesheet" type="text/css" />
	<link href="/stylesheets/office.css" rel="stylesheet" type="text/css" />

	<?php include('top_script.php'); ?>

	<!--[if IE]><link href="/stylesheets/v2-iefix.css" rel="stylesheet" type="text/css" /><![endif]-->
	<!--[if lte IE 6]><link href="/stylesheets/v2-ie6fix.css" rel="stylesheet" type="text/css" /><![endif]-->

	<script type="text/javascript" src="/javascript/jquery.js"></script>
	<script type="text/javascript" src="/javascript/ticker.js"></script>
	<script type="text/javascript">
	tickerInit('BarNews');
<?php if (!empty($ticker)) { ?>
	<?php for ($x = 1; $x < count($ticker); $x++) { ?>
	tickerAdd('<?php echo(xml_escape($ticker[$x]->headline)); ?>', '/<?php echo(xml_escape($ticker[$x]->section . '/' . $ticker[$x]->type . '/' . $ticker[$x]->id)); ?>');
	<?php } ?>
	tickerAdd('<?php echo(xml_escape($ticker[0]->headline)); ?>', '/<?php echo(xml_escape($ticker[0]->section . '/' . $ticker[0]->type . '/' . $ticker[0]->id)); ?>');
<?php } ?>
	onLoadFunctions.push(tickerStart);
	</script>

</head>

<body onload="onLoadHandler()" onunload="onUnloadHandler()">
	<div id="Header">
		<div id="HeaderItems">
			<div id="HeaderMenu">
				<?php
				// Set by GenerateToplinks in mainframe_helper
				if (isset($toplinks)) {
					foreach ($toplinks as $link) {
						if (is_string($link)) {
							echo(xml_escape($link). ' | ');
						} elseif (is_array($link)) {
							echo('<a href="' . xml_escape($link[1]) . '">' . xml_escape($link[0]) . '</a> | ');
						}
					}
				}
				?>
				<a href="/account/">my account</a>
			</div>
			<div id="HeaderTime">
				<?php echo($date['time']); ?>
			</div>
			<div id="HeaderDate">
				<div id="HeaderDay">
					<?php echo($date['day']); ?>
				</div>
				<div id="HeaderWeek">
					Week <?php echo($date['week']); ?>
				</div>
			</div>
		</div>
		<h1 id="HeaderLogo"><a href="/"><img src="/images/version2/frame/logo.png" alt="The Yorker" /></a></h1>
	</div>
	
	<div id="Bar">
		<div id="BarDate">
			<?php echo($date['date'] . ' ' . $date['month']); ?>
		</div>
		<div id="BarSearch">
			<form id="searchbox_003080001858553066416:dyddjbcpdlc" action="http://www.google.com/search">
				<input type="hidden" name="cx" value="003080001858553066416:dyddjbcpdlc" />
				<input type="hidden" name="cof" value="FORID:0" />
				<input name="q" type="text" size="25" value="Search for..." onfocus="inputFocus(this);" onblur="inputBlur(this);" />
			</form>
		</div>
		<div id="BarTicker">
			<span id="BarLatest">latest news:</span>
			<span id="BarNews">
				<?php if (!empty($ticker)) { ?>
				<a href="/<?php echo(xml_escape($ticker[0]->section . '/' . $ticker[0]->type . '/' . $ticker[0]->id)); ?>">
					<?php echo(xml_escape($ticker[0]->headline)); ?>
				</a>
				<?php } ?>
			</span>
		</div>
	</div>

<?php
$menu = array(
	array('home', '/', array('first')),
	array('news', '/news', array()),
	array('sport', '/sport', array()),
	array('arts', '/arts', array()),
	array('lifestyle', '/lifestyle', array('last')),
);
// Has a tab been set as selected?
$menu_style = '';
if (!empty($menu_tab)) {
	$menu_key = null;
	for ($x = 0; $x < count($menu); $x++) {
		if ($menu[$x][0] == $menu_tab) {
			$menu_key = $x;
			break;
		}
	}
	if ($menu_key !== null) {
		$menu[$menu_key][2][] = 'current';
		if (($menu_key - 1) > -1) {
			$menu[$menu_key - 1][2][] = 'next';
		} else {
			$menu_style = 'next';
		}
	}
}
?>

	<div id="Navigation">
		<ul id="Tabs"<?php if (!empty($menu_style)) echo(' class="' . $menu_style . '"'); ?>>
<?php foreach ($menu as $tab) { ?>
			<li<?php if (!empty($tab[2])) echo(' class="' . implode(' ', $tab[2]) . '"'); ?>><a href="<?php echo($tab[1]); ?>"><?php echo($tab[0]); ?></a></li>
<?php } ?>
<?php foreach ($links as $link) { ?>
			<li class="link"><?php echo($link); ?></li>
<?php } ?>
		</ul>
	</div>

	<div id="Page">
		<div id="MainBodyPane">
		
<!-- BEGIN generated content -->
<?php
	// TODO: check this works properly

	// Navigation bar
	if (isset($content['navbars']) && is_array($content['navbars'])) {
		foreach ($content['navbars'] as $navbar) {
			$navbar->Load();
		}
	} elseif (isset($content['navbar'])) {
		$content['navbar']->Load();
	}

	// Display each message
	foreach ($messages as $message) {
		// Display the message
		$message->Load();
	}

	// Display the main content
	$content[0]->Load();
?>
<!-- END generated content -->

			<div class="clear"></div>
		</div>
	</div>

	<?php include('footer.php'); ?>
</body>
</html>