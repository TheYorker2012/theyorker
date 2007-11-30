<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
	<h2>Actions</h2>
	<div class="Entry">
		<ul>
			<li><a href="/office/articletypes/create">Create New Article Type</a></li>
			<li><a href="/office/articletypes/create/skip">Create New With No Image</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>current subtypes</h2>
		<table>
			<thead>
				<tr>
					<th>Name</th><th>Parent</th><th>Img</th><th>Archive</th><th>Blurb</th><th>Order</th><th>Del</th>
				</tr>
			</thead>
			<?php
			foreach($sub_articles as $sub_article){
				echo('<tr>');
				echo('<td><a href="/office/articletypes/edit/'.$sub_article['id'].'">'.$sub_article['name'].'</a></td>');
				echo('<td>'.$sub_article['parent_name'].'</td>');
				echo('<td>');
				if($sub_article['image']==NULL){echo("<img src='/images/prototype/members/no9.png'>");}else{echo("<img src='/images/prototype/members/confirmed.png'>");}
				echo('</td>');
				echo('<td>');
				if($sub_article['in_archive']){echo("<img src='/images/prototype/members/confirmed.png'>");}else{echo("<img src='/images/prototype/members/no9.png'>");}
				echo('</td>');
				echo('<td>');
				if($sub_article['blurb']==''){echo("<img src='/images/prototype/members/no9.png'>");}else{echo("<img src='/images/prototype/members/confirmed.png'>");}
				echo('</td>');
				echo('<td>');
				echo("<a href='/office/articletypes/moveup/".$sub_article['id']."'><img src='/images/prototype/members/sortdesc.png'></a>");
				echo("<a href='/office/articletypes/movedown/".$sub_article['id']."'><img src='/images/prototype/members/sortasc.png'></a>");
				echo('</td>');
				echo('<td><a href="/office/articletypes/delete/'.$sub_article['id'].'">Del</a></td>');
				echo('</tr>');
			}
			?>
		</table>
	</div>
	<div class="BlueBox">
		<h2>shelved subtypes</h2>
		<table>
			<thead>
				<tr>
					<th>Name</th><th>Parent</th><th>Img</th><th>Archive</th><th>Blurb</th><th>Order</th><th>Del</th>
				</tr>
			</thead>
			<?php
			foreach($shelved_sub_articles as $sub_article){
				echo('<tr>');
				echo('<td><a href="/office/articletypes/edit/'.$sub_article['id'].'">'.$sub_article['name'].'</a></td>');
				echo('<td>'.$sub_article['parent_name'].'</td>');
				echo('<td>');
				if($sub_article['image']==NULL){echo("<img src='/images/prototype/members/no9.png'>");}else{echo("<img src='/images/prototype/members/confirmed.png'>");}
				echo('</td>');
				echo('<td>');
				if($sub_article['in_archive']){echo("<img src='/images/prototype/members/confirmed.png'>");}else{echo("<img src='/images/prototype/members/no9.png'>");}
				echo('</td>');
				echo('<td>');
				if($sub_article['blurb']==''){echo("<img src='/images/prototype/members/no9.png'>");}else{echo("<img src='/images/prototype/members/confirmed.png'>");}
				echo('</td>');
				echo('<td>');
				echo("<a href='/office/articletypes/moveup/".$sub_article['id']."'><img src='/images/prototype/members/sortdesc.png'></a>");
				echo("<a href='/office/articletypes/movedown/".$sub_article['id']."'><img src='/images/prototype/members/sortasc.png'></a>");
				echo('</td>');
				echo('<td><a href="/office/articletypes/delete/'.$sub_article['id'].'">Del</a></td>');
				echo('</tr>');
			}
			
			if(empty($shelved_sub_articles)) echo('<td colspan="7">No shelved article types.</td>');
			?>
		</table>
	</div>
</div>
