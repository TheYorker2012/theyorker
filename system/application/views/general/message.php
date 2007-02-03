<div style="margin-top: 5px;" class=" 
		<?php
			if ($class === 'error')
				echo 'warning_box"><img src="/images/prototype/homepage/warning.png" /';
			elseif ($class === 'warning')
				echo 'warning_box"><img src="/images/prototype/homepage/warning.png" /';
			elseif ($class === 'information')
				echo 'information_box"><img src="/images/prototype/homepage/infomark.png" /';
			elseif ($class === 'success')
				echo 'information_box"><img src="/images/prototype/homepage/infomark.png" /';
			else
				echo 'warning_box"><img src="/images/prototype/homepage/questionmark.png" /';
		?>
	;">
	<?php echo $text ?>
</div>
