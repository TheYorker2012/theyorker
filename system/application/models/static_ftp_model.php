<?php
/**
 *  @file static_ftp_model.php
 *  @author David Garbett (dg516)
 *  @todo Error handling (disable php handling and replace with nicer errors)
 *  Contains operations to connect to and handle ftp connection to static site
 */
class Static_ftp_model extends Model
{
	/// Basic constructor for model
	function StaticFtpModel()
	{
		parent::Model();
	}
	
	function Connect($passive = TRUE)
	{
		$conn_id = ftp_connect($this->config->item('static_ftp_address'));
		ftp_login($conn_id,
				$this->config->item('static_ftp_username'),
				$this->config->item('static_ftp_password'));
		$mode = ftp_pasv($conn_id, $passive);
		return $conn_id;
	}
	
	function GetList($conn_id, $folder='')
	{
		return ftp_nlist($conn_id,$folder);
	}
	
	function Upload($conn_id, $name, $local_file, $folder='',$binary=TRUE)
	{
		$list = $this->GetList($conn_id,$folder);
		while (in_array($name,$list))
		{
			$name = rand(0,9).$name;
		}
		ftp_put(
				$conn_id,
				$folder.'/'.$name,
				$local_file,
				($binary ? FTP_BINARY : FTP_ASCII));
		return $name;
	}

	function DeleteFile($conn_id, $file)
	{
		ftp_delete($conn_id,$file);
		return 0;
	}

	function Close($conn_id)
	{
		ftp_close($conn_id);
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
					} elseif ((count($exts) == 0) || ((($ext = strrchr($entry, '.')) !== FALSE) && (in_array(substr($ext, 1), $exts)))) {
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
}