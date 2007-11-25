<?php

/**
 * @file libraries/Irc_client.php
 * @brief Classes for managing web IRC proxy client.
 * @author James Hogan (jh559)
 * @see helpers/irc_defines_helper.php
 */

/// An irc client object which persistently serves other scripts.
class IrcClientManager
{
	/// string Name under which to store stuff in session.
	static $SessionName = 'yorkerirc';
	
	/// float Session poll interval in milliseconds.
	protected $PollInterval = 200;
	
	/// float Timeout in milliseconds.
	protected $Timeout = 30000;
	
	/// float The time we've slept for overall in milliseconds.
	protected $SleepTime = 0;
	
	/// file Socket file descriptor for connection to server.
	protected $Socket = NULL;
	
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
		
		$this->Socket = fsockopen($Server, $Port);
		if (false === $this->Socket) {
			$this->Socket = NULL;
		} else {
			$_SESSION[self::$SessionName]['connected'] = 1;
			stream_set_blocking($this->Socket, 0);
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
			$data = fgets($this->Socket, 128);
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
		if (NULL === $this->Socket) {
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
		if (NULL === $this->Socket) {
			return;
		}
		$this->SendLine("QUIT :$message\n");
		unset($_SESSION[self::$SessionName]['connected']);
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
				'address' => $from[1],
				'to' => $to,
				'channel' => $channel,
				'content' => $rest,
				'received' => time(),
			);
			$msg_words = preg_split('/ +/', $rest);
			switch ($action) {
				case 'PRIVMSG':
					$message['content'] = substr($rest,1);
					self::ProcessPrivMsg($message);
					break;
					
				case 'PART':
					$message['content'] = " has left this channel ($message[address])";
					break;
					
				case 'JOIN':
					$message['content'] = " has joined this channel ($message[address])";
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
				case IRC_RPL_NOTOPIC:
				case IRC_RPL_TOPIC:
					$message['channel'] = $msg_words[0];
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
							$message['names'][] = array('_tag' => 'name', 'nick' => $nick);
						}
					}
					break;
				case IRC_RPL_ENDOFNAMES:
					$message['channel'] = $msg_words[0];
					break;
				
				// In response to LINKS message
				case IRC_RPL_LINKS:
				case IRC_RPL_ENDOFLINGS:
					break;
			}
			
			// Translate numeric reply codes
			global $IrcReplyCodes;
			if (isset($IrcReplyCodes[$message['type']])) {
				$message['type'] = $IrcReplyCodes[$message['type']];
			}
			
			// Add to messages
			$messages[] = $message;
		}
	}
	
	/// Start listening to session data
	function listen()
	{
		if (NULL === $this->Socket) {
			return;
		}
		
		// Continue without php timeout
		set_time_limit(0);
		
		session_commit();
		$timeout = $this->Timeout;
		while ($timeout > 0) {
			$messages = array();
			// handle data from server
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
			
			session_start();
			if (isset($_SESSION[self::$SessionName])) {
				$session = & $_SESSION[self::$SessionName];
				
				foreach ($messages as $message) {
					$session['messages'][] = $message;
				}
				if (isset($session['requests']) && !empty($session['requests'])) {
					$timeout = $this->Timeout;
					
					// Serve request
					while (!empty($session['requests'])) {
						$request = $session['requests'][0];
						array_shift($session['requests']);
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
								$timeout = 0;
								break;
						}
					}
				}
			}
			session_commit();
			
			while ($this->JoinReady && !empty($this->JoinList)) {
				$this->SendLine('JOIN '.$this->JoinList[0]."\n");
				array_shift($this->JoinList);
			}
			
			// Wait for a small period of time
			usleep($this->PollInterval*1000);
			$this->SleepTime += $this->PollInterval;
			$timeout -= $this->PollInterval;
			
			if (!$this->JoinReady && $this->PollInterval > 1000) {
				$this->JoinReady = true;
			}
		}
		
		$this->Quit('The Yorker\'s AJAX IRC Client');
		fclose($this->Socket);
		$this->Socket = NULL;
	}
	
	/// Send a line to the server
	protected function SendLine($line)
	{
		fputs($this->Socket,$line);
	}
}

/// IRC Client class, for easily accessing yorker irc channels.
/**
 * @author James Hogan (jh559)
 */
class Irc_client
{
	/// Join channels using session.
	function Join($Channels)
	{
		$_SESSION[IrcClientManager::$SessionName]['requests'][] = array(
			'type' => 'join',
			'channels' => $Channels,
		);
	}
	/// Part channels using session.
	function Part($Channels)
	{
		$_SESSION[IrcClientManager::$SessionName]['requests'][] = array(
			'type' => 'part',
			'channels' => $Channels,
		);
	}
	
	function Ping()
	{
		$_SESSION[IrcClientManager::$SessionName]['requests'][] = array(
			'type' => 'ping',
		);
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
		$_SESSION[IrcClientManager::$SessionName]['requests'][] = array(
			'type' => 'post',
			'to' => $Channel,
			'message' => $Msg,
		);
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
	
	/// Disconnect.
	function Disconnect()
	{
		$_SESSION[IrcClientManager::$SessionName]['requests'][] = array(
			'type' => 'disconnect',
		);
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
		$timeout = $MaxWait;
		$interval = 200;
		// release the session, and wait for some time for new messages before returning.
		$messages = array();
		while (empty($messages) && $timeout > 0) {
			while (NULL !== ($message = $this->GetMessage())) {
				$messages[] = $message;
			}
			if (empty($messages)) {
				session_commit();
				usleep($interval*1000);
				session_start();
				$timeout -= $interval;
			}
		}
		
		return $messages;
	}
	
}

?>
