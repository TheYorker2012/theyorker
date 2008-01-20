<?php

function get_filenames($source_dir, $include_path = FALSE) {
	static $_filedata = array();
	
	if ($fp = @opendir($source_dir)) {
		while (FALSE !== ($file = readdir($fp))) {
			if (@is_dir($source_dir.$file) && substr($file, 0, 1) != '.')
				 get_filenames($source_dir.$file."/", $include_path);
			elseif (substr($file, 0, 1) != ".")
				$_filedata[] = ($include_path == TRUE) ? $source_dir.$file : $file;
		}
		return $_filedata;
	}
}

?>
