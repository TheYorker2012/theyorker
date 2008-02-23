<?php
/**
 * @file views/general/text.php
 * @brief Generic text view.
 * @author James Hogan (jh559)
 *
 * @param $Text The text to output.
 */
header('content-type: text/plain');

echo($Text);
?>