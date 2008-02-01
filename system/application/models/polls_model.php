<?php

// polls model

class Polls_model extends Model
{

	function Polls_Model()
	{
		parent::Model();
	}
	
	/*
	* this function gets the count of choices for the poll
	* @return	- poll's choice count
	*/
	function GetDisplayedPoll()
	{
		$sql = 'SELECT	poll_id
				FROM	polls
				WHERE	poll_displayed = 1
				AND		poll_deleted = 0';
		$query = $this->db->query($sql);
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->poll_id;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @brief Adds a new poll to the database 
	 */
	function AddNewPoll($question)
	{
		$sql = 'INSERT INTO
					polls (
						poll_question,
						poll_running,
						poll_displayed,
						poll_deleted,
						poll_created
						)
				VALUES (?, 0, 0, 0, CURRENT_TIMESTAMP)';
		$this->db->query($sql, array($question));
		return TRUE;
	}
	
	/*
	* this function gets a list of all non deleted polls in the database
	* @return	- id of the poll
			- poll's question 
			- poll's running status
			- poll's displayed status
			- poll's date of creation
	*/
	function GetListOfPolls()
	{
		$sql = 'SELECT	poll_id,
						poll_question,
						poll_running,
						poll_displayed,
						poll_created
				FROM	polls
				WHERE	poll_deleted = 0
				ORDER BY poll_created DESC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['id'] = $row->poll_id;
				$result_item['question'] = $row->poll_question;
				$result_item['is_running'] = $row->poll_running;
				$result_item['is_displayed'] = $row->poll_displayed;
				$result_item['created'] = $row->poll_created;
				$result[] = $result_item;
			}
		}
		return $result;
	}
	
	/*
	* this function gets a list of all non deleted polls 
	* which are running
	* @return	- id of the poll
			- poll's question
			- poll's displayed status
	*/
	function GetListOfRunningPolls()
	{
		$sql = 'SELECT	poll_id,
						poll_question,
						poll_displayed
				FROM	polls
				WHERE	poll_deleted = 0
				AND		poll_running = 1
				ORDER BY poll_question ASC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['id'] = $row->poll_id;
				$result_item['question'] = $row->poll_question;
				$result_item['is_displayed'] = $row->poll_displayed;
				$result[] = $result_item;
			}
		}
		return $result;
	}
	
	/*
	* this function gets the data relating to a given poll
	* @return	- poll's question 
			- poll's running status
			- poll's displayed status
			- poll's date of creation
			- poll's deletion status
	*/
	function GetPollDetails($poll_id)
	{
		$sql = 'SELECT	poll_question,
						poll_running,
						poll_displayed,
						poll_created,
						poll_deleted
				FROM	polls
				WHERE	poll_id = ?';
		$query = $this->db->query($sql,array($poll_id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return array(
				'question' => $row->poll_question,
				'is_running' => $row->poll_running,
				'is_deleted' => $row->poll_displayed,
				'created' => $row->poll_created,
				'deleted' => $row->poll_deleted
				);
		}
		else
		{
			return false;
		}
	}
	
	/*
	* this function gets the count of choices for the poll
	* @return	- poll's choice count
	*/
	function GetPollChoiceCount($poll_id)
	{
		$sql = 'SELECT	count(poll_choice_id) as poll_choice_count
				FROM	poll_choices
				WHERE	poll_choice_poll_id = ?
				AND		poll_choice_deleted = 0';
		$query = $this->db->query($sql,array($poll_id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->poll_choice_count;
		}
		else
		{
			return 0;
		}
	}
	
	/*
	* this function sets a poll to running
	*/
	function SetPollRunning($poll_id)
	{
		$sql = 'UPDATE	polls
				SET		poll_running = 1
				WHERE	poll_id = ?';
		$this->db->query($sql,array($poll_id));
		return true;
	}
	
	/*
	* this function sets a poll to not running
	*/
	function SetPollNotRunning($poll_id)
	{
		$sql = 'UPDATE	polls
				SET		poll_running = 0,
						poll_displayed = 0
				WHERE	poll_id = ?';
		$this->db->query($sql,array($poll_id));
		return true;
	}
	
	/*
	* this function sets a poll to displayed and all others to not displayed
	*/
	function SetPollDisplayed($poll_id)
	{
		$sql = 'UPDATE	polls
				SET		poll_displayed = 0';
		$this->db->query($sql);
		$sql = 'UPDATE	polls
				SET		poll_displayed = 1
				WHERE	poll_id = ?';
		$this->db->query($sql,array($poll_id));
		return true;
	}
	
	/*
	* this function sets no poll's to displayed
	*/
	function SetNoPollDisplayed()
	{
		$sql = 'UPDATE	polls
				SET		poll_displayed = 0';
		$this->db->query($sql);
		return true;
	}
	
	/*
	* this function deletes a poll
	*/
	function DeletePoll($poll_id)
	{
		$sql = 'UPDATE	polls
				SET		poll_deleted = 1
				WHERE	poll_id = ?';
		$this->db->query($sql,array($poll_id));
		return true;
	}
	
	/**
	 * @brief Adds a new poll choice to the database 
	 */
	function AddNewChoice($poll_id, $choice_name)
	{
		$sql = 'INSERT INTO
					poll_choices (
						poll_choice_poll_id,
						poll_choice_name
						)
				VALUES (?, ?)';
		$this->db->query($sql, array($poll_id, $choice_name));
		return TRUE;
	}
	
	/*
	* this function gets a list of a given poll's choices
	* @return	- id of the choice
			- name of the choice 
	*/
	function GetPollChoices($poll_id)
	{
		$sql = 'SELECT	poll_choice_id,
						poll_choice_name
				FROM	poll_choices
				WHERE	poll_choice_poll_id = ?
				AND		poll_choice_deleted = 0
				ORDER BY poll_choice_name ASC';
		$query = $this->db->query($sql,array($poll_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['id'] = $row->poll_choice_id;
				$result_item['name'] = $row->poll_choice_name;
				$result[] = $result_item;
			}
		}
		return $result;
	}
	
	/*
	* this function updates a poll choice with a new name
	*/
	function UpdatePollChoice($poll_choice_id, $name)
	{
		$sql = 'UPDATE	poll_choices
				SET		poll_choice_name = ?
				WHERE	poll_choice_id = ?';
		$this->db->query($sql,array($name, $poll_choice_id));
		return true;
	}
	
	/*
	* this function deletes a poll choice
	*/
	function DeletePollChoice($poll_choice_id)
	{
		$sql = 'UPDATE	poll_choices
				SET		poll_choice_deleted = 1
				WHERE	poll_choice_id = ?';
		$this->db->query($sql,array($poll_choice_id));
		return true;
	}
	
	/*
	* this function gets the polls choices and calculates the voting data for each
	*/
	function GetPollChoiceVotes($poll_id)
	{
		$sql = 'SELECT	poll_choice_id,
						poll_choice_name
				FROM	poll_choices
				WHERE	poll_choice_poll_id = ?
				AND		poll_choice_deleted = 0
				ORDER BY poll_choice_name ASC';
		$query = $this->db->query($sql,array($poll_id));
		$result = array();
		$vote_count = 0;
		$result['choices'] = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['id'] = $row->poll_choice_id;
				$result_item['name'] = $row->poll_choice_name;
				$sql = 'SELECT	count(poll_vote_poll_id) as option_vote_count
						FROM	poll_votes
						WHERE	poll_vote_poll_id = ?
						AND		poll_vote_poll_choice_id = ?
						AND		poll_vote_no_vote = 0';
				$query = $this->db->query($sql,array($poll_id, $row->poll_choice_id));
				$row = $query->row();
				$result_item['votes'] = $row->option_vote_count;
				$result['choices'][] = $result_item;
				$vote_count += $row->option_vote_count;
			}
		}
		$result['vote_count'] = $vote_count;
		return $result;
	}
	
	/*
	* this function finds out if the supplied choice is in the given poll
	*/
	function IsChoicePartOfPoll($poll_id, $choice_id)
	{
		$sql = 'SELECT	poll_choice_id
				FROM	poll_choices
				WHERE	poll_choice_poll_id = ?
				AND		poll_choice_id = ?';
		$query = $this->db->query($sql,array($poll_id, $choice_id));
		if ($query->num_rows() == 1)
			return true;
		else
			return false;
	}	
	
	/*
	* this function finds out if a user has voted on a poll
	*/
	function HasUserVoted($poll_id, $user_id)
	{
		$sql = 'SELECT	poll_vote_poll_id
				FROM	poll_votes
				WHERE	poll_vote_poll_id = ?
				AND		poll_vote_user_id = ?';
		$query = $this->db->query($sql,array($poll_id, $user_id));
		if ($query->num_rows() == 1)
			return true;
		else
			return false;
	}	
	
	/*
	* this function sets a user as not voting on a poll
	*/
	function SetUserPollVote($poll_id, $user_id, $choice_id)
	{
		$sql = 'INSERT INTO
					poll_votes (
						poll_vote_poll_id,
						poll_vote_user_id,
						poll_vote_poll_choice_id,
						poll_vote_no_vote
						)
				VALUES (?, ?, ?, 0)';
		$this->db->query($sql,array($poll_id, $user_id, $choice_id));
		return true;
	}
	
	/*
	* this function sets a user's vote for a option
	*/
	function SetUserPollNoVote($poll_id, $user_id)
	{
		$sql = 'INSERT INTO
					poll_votes (
						poll_vote_poll_id,
						poll_vote_user_id,
						poll_vote_poll_choice_id,
						poll_vote_no_vote
						)
				VALUES (?, ?, ?, 1)';
		$this->db->query($sql,array($poll_id, $user_id, NULL));
		return true;
	}
	

}