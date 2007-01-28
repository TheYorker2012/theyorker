<h2>Custom pages</h2>


<?php if ($permissions['custom_new']) { ?>
<a href="/admin/pages/custom/new">Create a new custom page</a><br /><br />
<?php } ?>

<?php
foreach ($custom as $page) {
	echo '<a href="/pages/'.$page['codename'].'">';
	echo $page['codename'];
	echo '</a>';
	echo ' (';
	echo '<a href="/admin/pages/custom/edit/'.$page['codename'].'">';
	echo 'edit';
	echo '</a>';
	if ($permissions['custom_delete']) {
		echo ', <a href="/admin/pages/custom/delete/'.$page['codename'].'">delete</a>';
	}
	echo ')';
	echo '<br />';
}
?>

<h2>Pages</h2>

<?php if ($permissions['page_new']) { ?>
<a href="/admin/pages/page/new">Create a new page</a><br /><br />
<?php } ?>

<?php
foreach ($pages as $page) {
	echo $page['codename'];
	echo ' (';
	echo '<a href="/admin/pages/page/edit/'.$page['codename'].'">';
	echo 'edit';
	echo '</a>';
	if ($permissions['page_delete']) {
		echo ', <a href="/admin/pages/page/delete/'.$page['codename'].'">delete</a>';
	}
	echo ')';
	echo '<br />';
}

?>
