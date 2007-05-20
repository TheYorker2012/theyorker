<div class="blue_box">
	<table>
		<th>
			<tr>
				<td>Image Type</td>
				<td>Null Image</td>
				<td>Options</td>
			</tr>
		</th>
		<tbody>
<?php if($imageType->num_rows() > 0) foreach ($imageType as $type) {?>
			<tr>
				<td><?=$type->image_type_name?></td>
				<td><?=$this->image->getImage(0, $type->image_type_codename)?></td>
				<td>
					<a href="<?=$type->image_type_codename?>">Edit</a>,
					<form action="<?=site_url($this->uri->uri_string())?>" method="post" enctype="multipart/form-data">
						<fieldset>
							<label for="upload">New null image</label>
							<input type="file" name="upload" /></br>
							<input type="submit" value="Upload" />
						</fieldset>
					</form>
				</td>
			</tr>
<?php } else { ?>
			<tr>
				<td colspan="3">There are no image types :(</td>
			</tr>
<?php } ?>
		</tbody>
	</table>
</div>