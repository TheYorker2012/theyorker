<?php
/**
 *  @file games_model.php
 *  @author David Garbett (dg516)
 *  Contains all Games db access functions
 */
	class Games_model extends Model
	{
		/// Basic constructor for model
		function GamesModel()
		{
			parent::Model();
		}
		
		/**
		 *  @brief Gets Game details for normal games page
		 *  @param argument1 Boolean: true if sort by play count, else by priority
		 *  @return array, indexed by id, of image_id and title
		 */
		function GetGamesList($bycount = TRUE)
		{
			$sql = '	SELECT		game_id,
									game_image_id,
									game_title
						FROM 		games
						WHERE 		game_activated=1
						ORDER BY	'.($bycount?'game_play_count':'game_priority');
									///  sort preference set by arg
			$query = $this->db->query($sql);
			$result = array();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$result[$row->game_id] = array(
						'image_id'	=> $row->game_image_id, 
						'title'		=> $row->game_title
						);
				}
			}
			return $result;
		}
		
		
		/**
		 *  @brief Gets a game for view
		 *  @param argument1 int: id of game wanted
		 *  @return array, of games title, filename, width and height, activated
		 */		
		function GetGame($game_id)
		{
			/// Increment play count
			$sql = '	UPDATE	games
						SET		game_play_count=game_play_count+1
						WHERE	game_id=?';
						
			$query = $this->db->query($sql,array($game_id));
		
			$sql = '	SELECT	game_title,
								game_filename,
								game_width,
								game_height,
								game_activated
						FROM	games
						WHERE	game_id=?';
			$query = $this->db->query($sql,array($game_id));
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				$result = array(
					'title'		=> $row->game_title,
					'filename'	=> $row->game_filename,
					'width'		=> $row->game_width,
					'height'	=> $row->game_height,
					'activated'	=> ($row->game_activated==1));
								/// replace activated status int (as in db) with boolean
			} else {
				/// game not found so return 0 (failure);
				$result = 0;
			}
			return $result;
		}
		
		
		/**
		 *  @brief Gets list of games for office
		 *  @param argument1 int: offset (for pagination)
		 *  @param argument2 int: number of games wanted (for pagination)
		 *  @return array, indexed by id, of title, play_count, date_added, priority, activated
		 */		
		function GetFullList($limit=0,$rows=0)
		{
			$sql = '	SELECT		game_id,
									game_image_id,
									game_title,
									game_activated,
									game_play_count,
									game_date_added,
									game_priority 
						FROM		games
						ORDER BY	game_title
						LIMIT		'.$limit.','.$rows;
			$query = $this->db->query($sql);

			if ($query->num_rows() > 0)
			
			/// Gets image xhtml here, so no manipluation needed in controller - change??
			$this->load->library('image');

			{
				foreach ($query->result() as $row)
				{
					/// type conversions occur here (i.e. date format, getting image xhtml)
					/// as result passed straight to view - change?
					$result[$row->game_id] = array(
						'image'			=> $this->image->getImage(
								$row->game_image_id,
								'medium',
								array('title' => $row->game_title)),
						'title'			=> $row->game_title,
						'play_count'	=> $row->game_play_count,
						'date_added'	=> date('j/m/y', $row->game_date_added),
						'priority'		=> $row->game_priority,
						'activated'		=> ($row->game_activated==1));
				}
			}
			return $result;
		}		
		
		/**
		 *  @brief Gets Number of rows in games table (number of games)
		 *  @return int: Number of rows
		 */		
		function GetCount()
		{
			$sql = '	SELECT COUNT(*) AS count
						FROM games';
			return $this->db->query($sql)->row()->count;
		}
		
		/**
		 *  @brief Toggle Activation state for a game
		 *  @param argument1 int: id of game to change
		 *  @return int: new state, or -1 on failure
		 */	
		function Toggle_Activation($game_id)
		{
			$sql = '	SELECT	game_activated
						FROM	games
						WHERE	game_id=?';
			
			$query = $this->db->query($sql,array($game_id));
			
			if ($query->num_rows() > 0)
			{
				$state = $query->row()->game_activated <> 1;
				$sql = '	UPDATE	games
							SET		game_activated = '.($state ? '1' : '0').'
							WHERE	game_id =?';
				$query = $this->db->query($sql,array($game_id));
			}else{
				$query=FALSE;
			}
			if (!$query)
			{
				return -1;
			}else{
				return $state;
			}
		}
		
		
		/**
		 *  @brief Removes a games entry from table
		 *  @param argument1 int: id of game to change
		 *  @return bool: true on success
		 *  @TODO all of it! (once file removal bit coded in controller)
		 */	
		function Del_Game($game_id)
		{
			return true;
		}
		
		/**
		 *  @brief Get Details of specified game for edit page
		 *  @param argument1 int: id of game being editted
		 *  @return array: of game title,filename,width,height,image_id ; or 0 on failure
		 */	
		function Edit_Game_Get($game_id)
		{
			$sql = '	SELECT	game_title,
								game_filename,
								game_width,
								game_height,
								game_image_id
						FROM	games
						WHERE	game_id=?';
			$query = $this->db->query($sql,array($game_id));
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				$result = array(
					'title'		=> $row->game_title,
					'filename'	=> $row->game_filename,
					'width'		=> $row->game_width,
					'height'	=> $row->game_height,
					'image_id'	=> $row->game_image_id);
			} else {
				$result = 0;
			}
			return $result;
		}
		
		/**
		 *  @brief Make Changes to game
		 *  @param argument1 int: id of game being editted
		 *  @param argument2 string: new title of game
		 *  @param argument3 int: new width of game
		 *  @param argument4 int: new height of game
		 *  @return bool: true on success
		 */	
		function Edit_Game_Update($game_id, $title, $width, $height)
		{
			$sql = '	UPDATE	games
						SET		game_title = ?,
								game_width = ?,
								game_height = ?
						WHERE	game_id = ?';
			return $this->db->query($sql,array($title,$width,$height,$game_id));
						
		}
	}
?>