<?php

/**
 * @file libraries/Irc_client.php
 * @brief Classes for managing web IRC proxy client.
 * @author James Hogan (jh559)
 * @see helpers/irc_defines_helper.php
 */

/// This uses the IPC library.
get_instance()->load->library('ipc');

/// An irc client object which persistently serves other scripts.
class IrcClientManager
{
	/// string Name under which to store stuff in session.
	static $SessionName = 'yorkerirc';
	
	/// float Session poll interval in milliseconds.
	protected $PollInterval = 200;
	
	/// float Timeout in seconds.
	protected $Timeout = 30;
	
	/// file Socket file descriptor for connection to server.
	protected $ServerSocket = NULL;
	
	/// IpcServer Client requests socket.
	protected $ClientRequests = NULL;
	
	/// string Buffer from server.
	protected $Buffer = '';
	
	/// bool Whether ready for joins.
	protected $JoinReady = false;
	
	/// arra[string] List of channels to join.
	protected $JoinList = array();
	
	/// Constructor
	/**
	 * @param $Server string Server url.
	 * @param $Port   int    Port number
	 */
	function __construct($Server, $Port = 6667)
	{
		get_instance()->load->helper('irc_defines');
		
		$this->ServerSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$connected = @socket_connect($this->ServerSocket, $Server, $Port);
		socket_set_nonblock($this->ServerSocket);
		if (!$connected) {
			$this->ServerSocket = NULL;
		} else {
			$this->ClientRequests = new IpcServer(Ipc::GenSockName('theyorkerirc'), session_id());
			if ($this->ClientRequests->Ready()) {
				$_SESSION[self::$SessionName]['connected'] = 1;
			} else {
				unset($_SESSION[self::$SessionName]['connected']);
			}
		}
	}
	
	/// Find whether a client is already connected
	static function IsConnected()
	{
		return isset($_SESSION[self::$SessionName]['connected']);
	}
	
	/// Return a line of data from server or NULL
	function GetSocketLine()
	{
		$finished = false;
		while (!$finished) {
			$data = socket_read($this->ServerSocket, 128);
			if (empty($data)) {
				$finished = true;
			} else {
				$this->Buffer .= $data;
			}
		}
		
		// Get first line in buffer
		$pos = strpos($this->Buffer, "\n");
		if (false !== $pos) {
			$line = substr($this->Buffer, 0, $pos);
			$this->Buffer = substr($this->Buffer, $pos+1);
			return $line;
		} else {
			return NULL;
		}
	}
	
	/// Login.
	function Login($user, $nick, $name)
	{
		if (NULL === $this->ServerSocket) {
			return;
		}
		
		if (strlen($user) > 10) {
			$user = substr($user,0,10);
		}
		
		$this->SendLine("USER $user james.albanarts.com JB :$name\n");
		$this->SendLine("NICK $nick\n");
		
		$_SESSION[self::$SessionName]['user'] = $user;
		$_SESSION[self::$SessionName]['name'] = $name;
		$_SESSION[self::$SessionName]['nick'] = $nick;
	}
	
	function Quit($message)
	{
		if (NULL === $this->ServerSocket) {
			return;
		}
		$this->SendLine("QUIT :$message\n");
		unset($_SESSION[self::$SessionName]['connected']);
	}
	
	function Join($channel)
	{
		$this->JoinList[] = $channel;
	}
	
	static function ProcessPrivMsg(& $Message)
	{
		$rest = $Message['content'];
		if (substr($rest, 0, 8) == chr(1).'ACTION ') {
			$Message['type'] = 'ACTION';
			$rest = substr($rest, 8);
			if (false !== ($pos = strpos($rest, chr(1)))) {
				$rest = substr($rest, 0, $pos);
			}
		}
		$preg_nick = preg_quote($_SESSION[self::$SessionName]['nick']);
		if (preg_match("/\b$preg_nick\b/i", $rest)) {
			$Message['highlight'] = true;
		}
		$Message['content'] = $rest;
	}
	
	static function ProcessMessage($line, & $messages)
	{
		$words = preg_split('/ +/', $line);
		if (substr($words[0],0,1) == ':') {
			// Normal private message
			$from = $words[0];
			$action = $words[1];
			$to = $words[2];
			// Split the from into nickname, username
			$from = substr($from,1);
			$from = explode('!', $from);
			// channel
			$channel = $to;
			if (substr($channel,0,1) != '#') {
				$channel = $from[0];
			}
			// Get the rest minus the beginning :
			$rest = implode(' ', array_slice($words, 3));
			$message = array(
				'type' => $action,
				'sender' => $from[0],
				'to' => $to,
				'channel' => $channel,
				'content' => $rest,
				'received' => time(),
			);
			if (isset($from[1])) {
				$message['address'] = $from[1];
			}
			$msg_words = preg_split('/ +/', $rest);
			$ignore = false;
			switch ($action) {
				case 'PRIVMSG':
					$message['content'] = substr($rest,1);
					self::ProcessPrivMsg($message);
					break;
					
				case 'PART':
					$message['content'] = " has left this channel ($message[address])";
					$message['names'][] = array(
						'_tag' => 'name',
						'_attr' => array('mode' => 'part'),
						'nick' => $message['sender'],
					);
					break;
					
				case 'JOIN':
					$message['content'] = " has joined this channel ($message[address])";
					$message['names'][] = array(
						'_tag' => 'name',
						'_attr' => array('mode' => 'join'),
						'nick' => $message['sender'],
					);
					break;
					
				case 'QUIT':
					$message['content'] = " has left this server (Quit: $rest)";
					break;
				
				
				case IRC_RPL_UNAWAY:  // You're no longer away
				case IRC_RPL_NOWAWAY: // You're now away
					break;
					
				// In response to WHOIS message
				case IRC_RPL_WHOISUSER:
				case IRC_RPL_WHOISSERVER:
				case IRC_RPL_WHOISOPERATOR:
				case IRC_RPL_WHOISIDLE:
				case IRC_RPL_ENDOFWHOIS:
				case IRC_RPL_WHOISCHANNELS:
					break;
					
				// In response to WHOWAS message
				case IRC_RPL_WHOWASUSER:
				case IRC_RPL_ENDOFWHOWAS:
					break;
					
				// In response to LIST message
				case IRC_RPL_LISTSTART:
				case IRC_RPL_LIST:
				case IRC_RPL_LISTEND:
					break;
				
				// In response to TOPIC message
				case IRC_RPL_CHANNELMODEIS:
					break;
				case IRC_RPL_NOTOPIC:
				case IRC_RPL_TOPIC:
					$message['channel'] = $msg_words[0];
					$message['topic'] = substr(implode(' ', array_slice($msg_words, 1)), 1);
					break;
				case 'TOPIC':
					$message['topic'] = substr($rest,1);
					$message['content'] = " set the channel topic to \"$message[topic]\"";
					break;
				
				// In response to INVITE message
				case IRC_RPL_INVITING:
					break;
				
				// In response to SUMMON message
				case IRC_RPL_SUMMONING:
					break;
				
				// Version information
				case IRC_RPL_VERSION:
					break;
				
				// In response to WHO message
				case IRC_RPL_WHOREPLY:
				case IRC_RPL_ENDOFWHO:
					break;
				
				// In response to NAMES message
				case IRC_RPL_NAMREPLY:
					if (preg_match('/^([=*@]) +(\S+) +:(.*)$/', $rest, $matches)) {
						list($all, $chan_mode, $channel, $nicks) = $matches;
						$message['channel'] = $channel;
						
						$nicks_list = preg_split('/ +/', $nicks);
						sort($nicks_list);
						foreach ($nicks_list as $nick) {
							switch (substr($nick, 0, 1)) {
								case '@':
								case '+':
									$nick = substr($nick, 1);
									break;
							}
							$message['names'][] = array(
								'_tag' => 'name',
								'nick' => $nick,
							);
						}
						if (isset($message['names'])) {
							$message['names']['_attr']['replace'] = 'yes';
						}
					}
					break;
				case IRC_RPL_ENDOFNAMES:
					$message['channel'] = $msg_words[0];
					break;
				
				// In response to LINKS message
				case IRC_RPL_LINKS:
				case IRC_RPL_ENDOFLINKS:
					break;
				
				case IRC_RPL_MOTDSTART:
				case IRC_RPL_MOTD:
				case IRC_RPL_ENDOFMOTD:
					$ignore = true;
					break;
			}
			
			// Translate numeric reply codes
			global $IrcReplyCodes;
			if (isset($IrcReplyCodes[$message['type']])) {
				$message['type'] = $IrcReplyCodes[$message['type']];
			}
			
			if (!$ignore) {
				// Add to messages
				$messages[] = $message;
			}
		}
	}
	
	/// Start listening to session data
	function listen()
	{
		if (NULL === $this->ServerSocket) {
			return;
		}
		
		// Continue without php timeout
		session_commit();
		set_time_limit(0);
		
		$messages = array();
		
		$client_connections = array();
		$latest_ping_client = NULL;
		$continue_main_loop = true;
		$timeout = strtotime('+10seconds');
		while ($continue_main_loop) {
			// try and dispatch any messages
			while ($latest_ping_client && !empty($messages)) {
				$next_message = $messages[0];
				if ($latest_ping_client->PutData($next_message)) {
					array_shift($messages);
				} else {
					$latest_ping_client->Close();
					foreach ($client_connections as $key => $connection) {
						if ($latest_ping_client === $connection) {
							unset($client_connections[$key]);
							break;
						}
					}
					// Await next ping
					$latest_ping_client = NULL;
					break;
				}
			}
			
			$read_sockets = array();
			$read_sockets[] = $this->ServerSocket;
			$read_sockets[] = $this->ClientRequests->Socket();
			foreach ($client_connections as $client) {
				if ($client->Ready()) {
					$read_sockets[] = $client->Socket();
				}
			}
			// Don't exceed timeout
			if (time() > $timeout) {
				break;
			}
			// Wait for data from one of the sockets
			if (1 > socket_select(
					$read_sockets,
					$write_sockets = array(),
					$exception_sockets = array(),
					5
				))
			{
				continue;
			}
			
			// handle data from server
			foreach ($read_sockets as $socket) {
				if ($socket == $this->ServerSocket) {
					while (NULL !== ($line = $this->GetSocketLine())) {
						// Get rid of trailing whitespace
						preg_match('/^(.*[^\s])\s*$/', $line, $matches);
						$line = $matches[1];
						
						$words = explode(' ', $line);
						if ($words[0] == 'PING') {
							$this->SendLine("PONG ".$words[1]."\n");
							if (!$this->JoinReady) {
								$this->JoinReady = true;
							}
						} elseif ($words[0] == 'NOTICE') {
							// Get the rest minus the beginning :
							$rest = implode(' ', array_slice($words, 2));
							$messages[] = array(
								'type' => 'NOTICE',
								'to' => $words[1],
								'content' => $rest,
								'received' => time(),
							);
						} else {
							self::ProcessMessage($line, $messages);
						}
					}
				}
				elseif ($socket == $this->ClientRequests->Socket()) {
					$new_connection = $this->ClientRequests->Accept();
					$client_connections[] = $new_connection;
				}
				else {
					$client_key = NULL;
					foreach ($client_connections as $key => $connection) {
						if ($connection->socket() == $socket) {
							$client_key = $key;
							break;
						}
					}
					$ipc_client = & $client_connections[$client_key];
					if (is_array($request = $ipc_client->GetData())) {
						$close_connection = true;
						switch ($request['type']) {
							case 'join':
								$this->JoinList[] = $request['channels'];
								break;
								
							case 'part':
								$this->SendLine("PART $request[channels]\n");
								break;
								
							case 'post':
								$this->SendLine("PRIVMSG $request[to] :$request[message]\n");
								break;
								
							case 'disconnect':
								$continue_main_loop = false;
								break;
							
							case 'ping':
								// Start using this connection for outgoing messages
								$latest_ping_client = $new_connection;
								$close_connection = false;
								$timeout = strtotime('+30seconds');
								break;
							
							case 'unping':
								// End using this connection for outgoing messages
								$latest_ping_client = NULL;
						}
						if ($close_connection) {
							$ipc_client->Close();
							unset($client_connections[$client_key]);
						}
					}
				}
			}
			
			while ($this->JoinReady && !empty($this->JoinList)) {
				$this->SendLine('JOIN '.$this->JoinList[0]."\n");
				array_shift($this->JoinList);
			}
			
			if (!$this->JoinReady && $this->PollInterval > 1000) {
				$this->JoinReady = true;
			}
		}
		
		// If a pinger is connected, signal it to give up
		if ($latest_ping_client !== null) {
			$latest_ping_client->PutData(array('_sig' => 'disconnect'));
		}
		
		// Close client connections
		$this->ClientRequests->Close();
		foreach ($client_connections as $connection) {
			$connection->Close();
		}
		// Quit IRC
		$this->Quit('The Yorker\'s AJAX IRC Client');
		
		// Close socket
		socket_close($this->ServerSocket);
		$this->ServerSocket = NULL;
	}
	
	/// Send a line to the server
	protected function SendLine($line)
	{
		socket_write($this->ServerSocket, $line);
	}
}

/// IRC Client class, for easily accessing yorker irc channels.
/**
 * @author James Hogan (jh559)
 */
class Irc_client
{
	/// IpcClient object.
	protected $connection = NULL;
	
	/// Default constructor.
	function __construct()
	{
	}
	
	/// Attached to manager script.
	function Attach()
	{
		if (NULL === $this->connection) {
			$this->connection = new IpcClient(Ipc::GenSockName('theyorkerirc'), session_id());
			if (!$this->connection->Ready()) {
				$this->connection = NULL;
			}
		}
	}
	
	/// Find whether connected to manager.
	function Attached()
	{
		return NULL !== $this->connection;
	}
	
	/// Join channels using session.
	function Join($Channels)
	{
		return $this->connection->PutData(array(
			'type' => 'join',
			'channels' => $Channels,
		));
	}
	/// Part channels using session.
	function Part($Channels)
	{
		return $this->connection->PutData(array(
			'type' => 'part',
			'channels' => $Channels,
		));
	}
	
	function Nick($NewNick)
	{
		return $this->connection->PutData(array(
			'type' => 'nick',
			'nick' => $NewNick,
		));
	}
	
	function Ping()
	{
		return $this->connection->PutData(array(
			'type' => 'ping',
		));
	}
	
	function Unping()
	{
		return $this->connection->PutData(array(
			'type' => 'unping',
		));
	}
	
	/// Interpret a query in a channel.
	function InterpretQuery($Channel, $Query)
	{
		$words = preg_split('/ +/', $Query);
		if (substr($words[0], 0,1) == '/') {
			// Various commands being with /
			$words[0] = strtolower($words[0]);
			switch (substr($words[0],1)) {
				case 'me':
					$Query = chr(1)."ACTION ".substr($Query,4).chr(1);
					break;
					
				case 'join':
					$this->Join(implode(' ', array_slice($words, 1)));
					return NULL;
					
				case 'part':
					if (count($words) > 1) {
						$this->Part(implode(' ', array_slice($words, 1)));
					} else {
						$this->Part($Channel);
					}
					return NULL;
					
				case 'nick':
					/// @todo handle /nick
					return array(
						'type' => 'NOTICE',
						'to' => $_SESSION[IrcClientManager::$SessionName]['user'],
						'channel' => $Channel,
						'content' => "/nick not yet supported",
						'received' => time(),
					);
					return NULL;
					
				default:
					return array(
						'type' => 'NOTICE',
						'to' => $_SESSION[IrcClientManager::$SessionName]['user'],
						'channel' => $Channel,
						'content' => "Unrecognised command: $words[0]",
						'received' => time(),
					);
			}
		}
		return $this->PostChannelMessage($Channel, $Query);
	}
	
	/// Post a message to a channel.
	function PostChannelMessage($Channel, $Msg)
	{
		$success = $this->connection->PutData(array(
			'type' => 'post',
			'to' => $Channel,
			'message' => $Msg,
		));
		if (!$success) {
			return false;
		} else {
			$message = array(
				'type' => 'PRIVMSG',
				'sender' => $_SESSION[IrcClientManager::$SessionName]['nick'],
				'to' => $Channel,
				'channel' => $Channel,
				'content' => $Msg,
				'received' => time(),
			);
			IrcClientManager::ProcessPrivMsg($message);
			return $message;
		}
	}
	
	/// Disconnect.
	function Disconnect()
	{
		return $this->connection->PutData(array(
			'type' => 'disconnect',
		));
	}
	
	/// Forcefully disconnect.
	function ForceDisconnect()
	{
		// Set session message to disconnect, and allow other servers to die.
		$this->Disconnect();
		session_commit();
		sleep(2);
		session_start();
		unset($_SESSION[IrcClientManager::$SessionName]);
	}
	
	/// Get a channel message or NULL.
	function GetMessage()
	{
		if (isset($_SESSION[IrcClientManager::$SessionName]['messages']) &&
			!empty($_SESSION[IrcClientManager::$SessionName]['messages']))
		{
			$message = $_SESSION[IrcClientManager::$SessionName]['messages'][0];
			array_shift($_SESSION[IrcClientManager::$SessionName]['messages']);
			return $message;
		} else {
			return NULL;
		}
	}
	
	/// Wait up to $MaxWait milliseconds for new messages.
	function WaitForMessages($MaxWait = 5000)
	{
		if (NULL === $this->connection) {
			return NULL;
		}
		$messages = array();
		$seconds = 1;
		$useconds = 0;
		while ($MaxWait > 0)
		{
			$ready = socket_select($rd = array($this->connection->Socket()), $wr=array(),$ex=array($this->connection->Socket()),
							$seconds, $useconds) > 0;
			$MaxWait -= $seconds*1000;
			if ($ready) {
				$message = $this->connection->GetData();
				if (is_array($message)) {
					// The manager is telling us to disconnect:
					if (isset($message['_sig']) && $message['_sig'] == 'disconnect') {
						return $messages;
					}
					$messages[] = $message;
				}
				$seconds = 0;
				$useconds = 15;
			} elseif (!$seconds) {
				break;
			}
		}
		return $messages;
	}
	
}

?>
