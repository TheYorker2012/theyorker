<div id="RightColumn">
	<?php if (isset($main_text)) { ?>
		<h2 class="first">What's this?</h2>
		<p><?php echo $main_text; ?></p>
	<?php } ?>
	<h2>Inline edit mode</h2>
	<?php echo($inline_edit_text); ?>
	<ul>
		<li><?php if ($this->pages_model->GetInlineEditMode()) {
			echo('<a href="'.site_url('admin/pages/inline/off').$this->uri->uri_string().'">Disable inline edit mode</a>');
		} else {
			echo('<a href="'.site_url('admin/pages/inline/on').$this->uri->uri_string().'">Enable inline edit mode</a>');
		} ?></li>
	</ul>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>Custom pages</h2>

<?php if ($permissions['custom_new']) { ?>
	<p><a href="/admin/pages/custom/new">Create a new custom page</a></p>
<?php } ?>
	<p><?php
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
	?></p>
	</div>

	<div class="BlueBox">
		<h2>Pages</h2>

		<p><a href="/admin/pages/common">Common Properties</a></p>

		<?php if ($permissions['page_new']) { ?>
		<p><a href="/admin/pages/page/new">Create a new page</a></p>
		<?php } ?>

		<p><?php
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
		?></p>
	</div>
</div>