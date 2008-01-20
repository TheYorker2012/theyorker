<?php

/**
 * @file libraries/Ipc.php
 * @brief IPC library
 * @author James Hogan <james@albanarts.com>
 */

/// IPC based socket.
class IpcClient
{
	/// The socket descriptor.
	protected $sockname;
	
	/// The actual socket.
	protected $sock;
	
	/// The password.
	protected $password;
	
	/// Primary constructor.
	function __construct($sockname, $password)
	{
		$this->password = $password;
		if (is_resource($sockname)) {
			$this->sock = $sockname;
		}
		else {
			assert('is_string($sockname)');
			$this->sockname = $sockname;
			$this->sock = socket_create(AF_UNIX, SOCK_STREAM, 0);
			$success = @ socket_connect($this->sock, $this->sockname);
			if (false === $success) {
				$this->sock = NULL;
			}
		}
	}
	
	/// Destructor.
	function __destruct()
	{
		$this->Close();
	}
	
	/// Is the connection ready?
	function Ready()
	{
		return is_resource($this->sock);
	}
	
	function Socket()
	{
		return $this->sock;
	}
	
	/// Close the connection;
	function Close()
	{
		if (is_resource($this->sock)) {
			socket_close($this->sock);
			$this->sock = NULL;
		}
	}
	
	/// Put php data.
	function PutData($data)
	{
		// Prepare message
		$block = array($data, $this->password);
		$message = serialize($block);
		$message = strlen($message).':'.$message;
// 		var_dump($message);
		while (strlen($message)) {
			$result = @ socket_write($this->sock, $message);
			if (false === $result) {
				return false;
			}
			// trip first $result bytes from string and try again
			$message = substr($message, $result);
		}
		return true;
	}
	
	/// Get php data.
	function GetData($password = NULL)
	{
		// Get the size of the data block
		$size = @ socket_read($this->sock, 1);
		if (false === $size || !strlen($size)) {
			return NULL;
		}
		$finished = false;
		while (!$finished) {
			$nextchar = socket_read($this->sock, 1);
			if (false === $nextchar) {
				return NULL;
			}
			if (!strlen($nextchar)) {
				usleep(10*1000);
			}
			elseif ($nextchar == ':') {
				$finished = true;
			}
			else {
				$size .= $nextchar;
			}
		}
		if (!is_numeric($size)) {
			// !!! This shouldn't happen, close the socket
			return false;
		}
		
		$data_block = '';
		while ($size > 0) {
			$data = socket_read($this->sock, $size);
			if (false === $data) {
				return NULL;
			}
			$data_block .= $data;
			
			$length = strlen($data);
			if ($length == 0) {
				usleep(10*1000);
			}
			else {
				$size -= $length;
			}
		}
		$block = unserialize($data_block);
		if (!isset($block[0])) {
			// all received should have a data block
			return false;
		}
		// A password is required, and should have been provided
		if (!isset($block[1]) || $block[1] !== $this->password) {
			die("Wrong password!");
			var_dump($block);
			var_dump($this->password);
			return false;
		}
		return $block[0];
	}
}

/// IPC based server.
class IpcServer
{
	/// string Identifier filename.
	protected $ident;
	
	/// socket Client socket file desctriptor.
	protected $server_sock;
	
	/// string Password for all communciation.
	protected $password;
	
	/// Default constructor
	function __construct($sockname, $password)
	{
		// Set the password
		$this->password = $password;
		
		// Make a named request socket
		$this->server_sock = socket_create(AF_UNIX, SOCK_STREAM, 0);
		
		// Name the server
		$this->ident = $sockname;
		@ unlink($this->ident);
		if (! @ socket_bind($this->server_sock, $this->ident)) {
			$this->Close();
		} else {
			socket_listen($this->server_sock);
		}
	}
	
	/// Destroy server
	function __destruct()
	{
		$this->Close();
	}
	
	/// Find whether the server is ready
	function Ready()
	{
		return NULL !== $this->server_sock;
	}
	
	/// Close connections + unlinke socket file
	function Close()
	{
		/// Remove socket
		if ($this->server_sock != NULL) {
			socket_close($this->server_sock);
			@ unlink($this->ident);
		}
		$this->server_sock = NULL;
	}
	
	/// Get the socket resource
	function Socket()
	{
		return $this->server_sock;
	}
	
	/// Accept next connection.
	function Accept()
	{
		$new_connection = socket_accept($this->server_sock);
		if (is_resource($new_connection)) {
// 			socket_set_nonblock($new_connection);
			return new IpcClient($new_connection, $this->password);
		} else {
			return NULL;
		}
	}
	
	/// Get the server identifier.
	function Identifier()
	{
		return $this->ident;
	}
}

class Ipc
{
	/// Get the name of a socket, hashed unique to this session.
	static function GenSockName($label)
	{
		return sys_get_temp_dir().'/'.$label.'_'.sha1(session_id()).'.sock';
	}
}

?>