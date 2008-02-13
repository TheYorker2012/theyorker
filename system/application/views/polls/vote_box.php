<?php

/**
 * @file views/polls/vote_box.php
 * @brief Library for assembling poll data and displaying polls.
 * @author Richard Ingle (ri504@york.ac.uk)
 * @note Moved from polls_view library and viewified by
 *       James Hogan (james_hogan@theyorker.co.uk)
 */
 
?>
<h2>
	Poll
</h2>
<?php

if ($this->user_auth->isLoggedIn)
{
	if ($view->user_voted)
	{
		?><div class="Entry"><?php
		echo(xml_escape($view->data['question']));
		echo(' ('.$view->option_data['vote_count'].' votes)');
		if (count($view->option_data['choices']) > 0) {
			?><ul><?php
			foreach ($view->option_data['choices'] as $choice) {
				if ($view->option_data['vote_count'] > 0)
				{
					$percentage = round($choice['votes']/$view->option_data['vote_count']*100);
				}
				else
				{
					$percentage = 0;
				}
				$percentage = ' - '.$percentage.'%';
				?><li><?php
				echo(xml_escape($choice['name']).$percentage);
				?></li><?php
			}
			?></ul><?php
		}
		?></div><?php
	}
	else
	{ ?>
		<form class="form" action="<?php echo($_SERVER['REQUEST_URI']); ?>"
		      method="post">
			<div class="Entry">
				<fieldset>
					<?php
					echo(xml_escape($view->data['question']));
					if ($view->show_results)
					{
						if ($view->option_data['vote_count']) {
							echo(' ('.$view->option_data['vote_count'].' votes)');
						}
						else {
							echo(' (no votes)');
						}
					}
					if (count($view->option_data['choices']) > 0) {
						foreach ($view->option_data['choices'] as $choice) {
							if ($view->show_results)
							{
								if ($view->option_data['vote_count'] > 0)
								{
									$percentage = round($choice['votes'] /
										$view->option_data['vote_count']*100);
									$percentage = ' - '.$percentage.'%';
								}
								else
								{
									$percentage = '';
								}
							}
							else
							{
								$percentage = '';
							}
							?>
							<label>
								<input class="checkbox" type="radio" name="poll_vote"
								       value="<?php echo($choice['id']); ?>" />
								<?php echo(xml_escape($choice['name']).$percentage); ?>
							</label>
							<?php
						}
					}
					?>
				</fieldset>
				<fieldset>
					<?php
					if (!$view->show_results)
					{
						?>
						<input class="button" type="submit"
						       name="submit_results" value="Results" />
						<?php
					}
					?>
					<input class="button" type="submit" name="submit_vote"
					       value="Vote" />'
				</fieldset>
			</div>
		</form>
		<?php
	}
}

// Not logged in so can't vote until done sox	
else
{ ?>
	<div class="Entry">
		<?php echo(xml_escape($view->data['question'])); ?>
	</div>
	<div class="Entry">
		You must
		<a href="<?php echo(xml_escape($this->site_links->login())); ?>">
			login
		</a>
		to vote.
	</div>
	<?php
}
?>