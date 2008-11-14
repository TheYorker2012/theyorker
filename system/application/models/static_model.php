<?php
class Static_model extends Model
{
	/// Basic constructor for model
	function StaticModel()
	{
		parent::Model();
	}

	/**
	 *	@brief		Direct PHP method to obtaining all the files in a given directory (and subdirectories)
	 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
	 *	@param		$path - String path of directory to search (e.g. /home/yorker/static/media/)
	 *	@param		$link - A string to prepend to the paths returned (e.g. http://static.theyorker.co.uk/)
	 *	@param		$exts - If set, only files with extensions in this array are returned
	 *	@return		$files - An array containing the paths to all matching files
	 */
	function GetDirectoryListing ($path, $link = '', $exts = array())
	{
		$files = array();
		$dirs = array();

		if (is_dir($path)) {
			$d = dir($path);
			while (($entry = $d->read()) !== FALSE) {
				if (($entry != '.') && ($entry != '..')) {
					if (is_dir($path . '/' . $entry)) {
						$dirs[] = $entry;
					} elseif ((count($exts) == 0) || ((($ext = strrchr($entry, '.')) !== FALSE) && (in_array(strtolower(substr($ext, 1)), $exts)))) {
						$files[] = $link . '/' . $entry;
					}
				}
			}
			$d->close();
			foreach ($dirs as $dir) {
				$files = array_merge($files , $this->GetDirectoryListing($path . '/' .$dir, $link . '/' . $dir, $exts));
			}
		}
		return $files;
	}
	
	function MoveFile ($path_from,$path_to)
	{
		$result = false;
		$path_to = rtrim($path_to,'/\\');
		if(is_file($path_from) && !is_file($path_to) && !is_link($path_to))
		{
			$result = rename(
				$path_from,
				(is_dir($path_to) ? 
					$path_to.'/'.basename($path_from) :
					$path_to));
		}
		return $result;
	}
	
	function GetFileSize ($file_path)
	{
		$result = false;
		if(is_file($file_path))
		{
			$result = filesize($file_path);
		}
		return $result;
	}
}

?>
