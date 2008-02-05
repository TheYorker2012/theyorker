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
		
	}	
			
