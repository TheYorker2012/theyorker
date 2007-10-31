 
<div class='BlueBox'>
	<h2><?php echo($section_games['title']); ?></h2>
	<p><?php echo($section_games['text']); ?></p>
</div>

<div class='BlueBox'>
	<!-- tables used for quick layout - replace with styled div's? -->
	<table><tr>
		<?php 
			$column = 0;
			$column_max = 6;
			foreach ($games as $game_id => $game)
			{
				if ($column	> $column_max)
				{
					echo('</tr><tr>');
					$column = 0;
				}
				echo('<td><a href=/games/view/'.$game_id.'>');
				echo($game['image']);
				echo('</a></td>');
				$column = $column +1;
			}
		?>
	</tr></table>
</div>
<small><?php echo($section_games['footer']); ?></small>
