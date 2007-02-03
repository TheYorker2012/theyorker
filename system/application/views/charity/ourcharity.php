<div class='RightToolbar'>
	<h4>Our Goal</h4>
	<div class='Entry'>
		<h5>What Are We Aiming For?</h5>
		<p><?php echo $sections['charity']['goal_text']; ?></p>
	</div>
	
	<h4>Funding</h4>
	<div class='Entry'>
		<h5>Current Money<br />£<?php echo $sections['charity']['total']; ?></h5>
		<h5>Amount Needed<br />£<?php echo $sections['charity']['goal']; ?></h5>
		<h5>Donate Now!!!</h5>
	</div>
	
	<h4><?php echo $sections['sidebar_related']['title']; ?></h4><!--Next 2 sections basically the same with different data and links-->
	<p style="margin-top: 0px; padding: 8px;">
	<?php
        foreach ($sections['article']['related_articles'] as $related_articles)
	{
		echo '<b><a href="http://www.google.com/">'.$related_articles['heading'].'</a></b><br />';
	};
	?>
	</p>

	<h4><?php echo $sections['sidebar_external']['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">
	<?php
        foreach ($sections['article']['links'] as $links)
	{
		echo '<b><a href="'.$links['url'].'">'.$links['name'].'</a></b><br />';
	};
	?>
	</p>
</div>





<div class='grey_box'>
	<h2><?php echo $sections['article']['heading']; ?></h2>
	<span class="black"><?php echo $sections['article']['text']; ?></span>
</div>


<?php
if (isset($sections['progress_reports']['entries']))
{
	echo '<div class="blue_box">';
	echo '<span style="font-size: x-large;  color: #BBBBBB; ">'.$sections['progress_reports']['title'].'</span><br />';
	foreach ($sections['progress_reports']['entries'] as $pr_entry)
	{
		echo '<br>';
		echo '<span style="font-size: large;  color: #BBBBBB; ">'.$pr_entry['date'].'</span><br />';
		echo $pr_entry['text'].'<br />';
	}
	echo '</div>';
}
?>

<div class='grey_box'>
	<h2>What You Can Do To Help</h2>
	Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin ante nibh, convallis non, rutrum vel, pretium vel, lectus. Aliquam congue malesuada augue. Duis tellus. Integer arcu odio, scelerisque a, mattis a, interdum porta, ante. Nulla diam. Fusce nisl sapien, mattis quis, sagittis in, auctor id, sem. Etiam congue dolor vitae neque. Praesent libero metus, aliquet vel, lobortis eget, porta et, justo.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus.
</div>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>