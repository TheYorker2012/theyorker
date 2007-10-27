<?php if (count($facebook_bylines) == 0) { ?>
		<div style="color:red">
			Before you can view the "My Articles" section you must link at least one of your
			bylines with your facebook account.
		</div>
<?php } ?>
		<div>
			Below are a list of all the bylines belonging to your Yorker user account.
			The "My Articles" section is populated with the articles written using any
			of your bylines which you select to "link with facebook".
		</div>

<?php foreach ($user_bylines as $byline) { ?>
		<div style="margin-top:10px;border-bottom:1px solid #bbb;">
			<div style="border:1px #20c1f0 solid">
				<?php $this->load->view('/office/bylines/byline', $byline); ?>
			</div>
			<ul style="padding:5px 0;list-style-image:url('http://www.theyorker.co.uk/images/prototype/homepage/arrow.png');list-style-position:inside;margin-left:0;">
				<?php if ($byline['business_card_facebook_link']) { ?>
					<li>
						To no longer list the articles you write with this byline under the 'My Articles' section:
						<a href="http://apps.facebook.com/theyorker/myarticles/bylines/unlink/<?php echo($byline['business_card_id']); ?>/">
							Un-link this byline with Facebook
						</a>
					</li>
				<?php } else { ?>
					<li>
						To list the articles you write with this byline under the 'My Articles' section:
						<a href="http://apps.facebook.com/theyorker/myarticles/bylines/link/<?php echo($byline['business_card_id']); ?>/">
							<b>Link this byline with Facebook</b>
						</a>
					</li>
				<?php } ?>
				<li>
					When people click on my "View my articles" profile action, take them to the news
					archive and list all the articles i've written using this byline by clicking
					<a href="http://apps.facebook.com/theyorker/myarticles/bylines/action/<?php echo($byline['business_card_id']); ?>/">here</a>.
				</li>
			</ul>
		</div>
<?php } ?>