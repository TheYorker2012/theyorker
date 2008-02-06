<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
	<h2>Actions</h2>
	<div class="Entry">
		<ul>
			<li><a href="/office/leagues/create">Create New League</a></li>
			<li><a href="/office/leagues/create/skip">Create New With No Image</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>current leagues</h2>
		<table>
			<thead>
				<tr>
					<th>Name</th><th>Parent</th><th align="center">Image</th><th align="center">Size</th><th align="center">Tags</th><th>Order</th><th>Edit</th><th>Del</th>
				</tr>
			</thead>
			<?php
			foreach($leagues as $league){
				echo('<tr>');
				echo('<td>');
				if($league['current_size'] > 0){
					echo('<a href="/office/league/edit/'.$league['id'].'">'.$league['name'].'</a>');
				}else{
					echo($league['name']);
				}
				echo('</td>');
				echo('<td>'.$league['section_name'].'</td>');
				echo('<td align="center">');
				if($league['image_id']==NULL){echo("<img src='/images/prototype/members/no9.png'>");}else{echo("<img src='/images/prototype/members/confirmed.png'>");}
				echo('</td>');
				echo('<td align="center">');
				if($league['current_size'] > $league['size'] || $league['current_size']==0){
					echo('<span class="red">'.$league['current_size'].'/'.$league['size'].'</span>');
				}else{
					echo($league['current_size'].'/'.$league['size']);
				}
				echo('</td>');
				echo('<td align="center">');
				if($league['number_of_tags']>0){
					echo $league['number_of_tags'];
				}else{
					echo('<span class="red">'.$league['number_of_tags'].'</span>');
				}
				echo('</td>');
				echo('<td>');
				echo("<a href='/office/leagues/moveup/".$league['id']."'><img src='/images/prototype/members/sortdesc.png'></a>");
				echo("<a href='/office/leagues/movedown/".$league['id']."'><img src='/images/prototype/members/sortasc.png'></a>");
				echo('</td>');
				if($league['autogenerated']==0){
				echo('<td><a href="/office/leagues/edit/'.$league['id'].'">Edit</a></td>');
				echo('<td><a href="/office/leagues/delete/'.$league['id'].'">Del</a></td>');
				}else{
				echo('<td>&nbsp;</td><td>&nbsp;</td>');
				}
				echo('</tr>');
			}
			?>
		</table>
	</div>
</div>