<div id='newsnav'>
	<ul id='newsnavlist'>
	<li><a href='/directory/fragsoc/'<?php if (''===$page) echo ' id="current"'; ?>>
		<img src='/images/prototype/news/uk.png' alt='About' title='About' /> About</a></li>
	<li><a href='/directory/fragsoc/events/'<?php if ('events'===$page) echo ' id="current"'; ?>>
		<img src='/images/prototype/news/feature.gif' alt='Events' title='Events' /> Events</a></li>
	<li><a href='/directory/fragsoc/members/'<?php if ('members'===$page) echo ' id="current"'; ?>>
		<img src='/images/prototype/news/feature.gif' alt='Members' title='Members' /> Members</a></li>
	<li><a href='/directory/fragsoc/reviews/'<?php if ('reviews'===$page) echo ' id="current"'; ?>>
		<img src='/images/prototype/news/feature.gif' alt='Reviews' title='Reviews' /> Reviews</a></li>
	</ul>
</div>
<h2><?php echo $organisation['name']; ?></h2>
<?php $content[0]->Load(); ?>
<a href='/directory/'>Back to the directory</a>