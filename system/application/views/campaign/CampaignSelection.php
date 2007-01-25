	<a href="<?php echo site_url('campaign/test'); ?>">Campaign page 2 (once campaign has been selected)</a><Br>
	<br>
	<div class="wholepage2"> <!-- Remove This Hack Sometime -->
	<div class="title">CAMPAIGN</div>
	<div class="Blurb"><img src="/images/prototype/campaign/field.jpg"><br><br>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.</div>
	<div class="subtitle">CHOOSE YOUR CAMPAIGN</div>
	<div class="subsubtitle">CLICK ON THE CAMPAIGN TO FIND OUT MORE</div><Br>
	<div class="Container">
	<div class="HalfBox">
		<b>
			<?php
                        foreach ($data['vars']['Campaign_List'] as $campaigns)
                        {
				echo '<a href="'.site_url('campaign/details/').'/'.$campaigns['id'].'">'.$campaigns['name'].'</a><br/>';
			}
			?>
		</b>
	</div>
	<div class="HalfBox">
		<div style="text-align:center;font-weight: bold;font-size: 18px;">OR VOTE NOW</div>
		<div style="padding-left:80px;">
		<form style="display: inline;">
		<?php
                foreach ($data['vars']['Campaign_List'] as $campaigns)
                {
			echo '<input type="radio" value="'.$campaigns['id'].'" name="vote">'.$campaigns['name'].'<br/>';
		}
		?>
		<input style="width: 75%;" type="submit" value="Vote">
		</form>
	</div></div>
	<div class="breaker"></div> <!-- Remove This Hack Sometime -->
	</div>
	<div class="subtitle">CURRENT RESULTS</div>
	<div class="subsubtitle">CHOISE DEADLINE FOR STUDENTS IS <?php echo $DeadLine; ?></div><br>
	<div style="float: left;">
		<b>
			<?php
                        foreach ($data['vars']['Campaign_List'] as $campaigns)
                        {
				echo '<a href="'.site_url('campaign/details/').'/'.$campaigns['id'].'">'.$campaigns['name'].'</a><br/>';
			}
			?>
		</b>
	</div>
	<div style="float: left; padding-left: 30px;">
		<?php
                foreach ($data['vars']['Campaign_List'] as $campaigns)
                {
			echo '<div class="votebar" style="width: 50px;"></div>'.$campaigns['votes'].'<br/>';
		}
		?>
	</div>
	<div class="breaker"></div> <!-- Remove This Hack Sometime -->
	</div>
	</div>
