<?php
/**
 * This model retrieves data for the Campaign pages.
 *
 * @author Richard Ingle (ri504)
 * 
 */
 
//TODO - prevent erros if no data present
 
class Charity_model extends Model
{
	function CharityModel()
	{
		//Call the Model Constructor
		parent::Model();
	}
	
	/**
	 * returns true if the charity with id = charity_id exists
	 */
	function CharityExists($charity_id)
	{
		$sql = 'SELECT	charity_id
				FROM	charities
				WHERE	charity_id = ?';
		$query = $this->db->query($sql,array($charity_id));
		if ($query->num_rows() == 1)
			return TRUE;
		else
			return FALSE;
	}
	
	/**
	 * gets the charities name
	 * @return charity name
	 * @pre charity with id = charity_id exists
	 */
	function GetCharityName($charity_id)
	{
		$sql = 'SELECT	charity_name
				FROM	charities
				WHERE	charity_id = ?';
		$query = $this->db->query($sql,array($charity_id));
		$row = $query->row();
		return $row->charity_name;
	}
	
	/**
	 * gets the specified charitys article id
	 * @return charity name
	 * @pre charity with id = charity_id exists
	 */
	function GetCharityArticleId($charity_id)
	{
		$sql = 'SELECT	charity_article_id
				FROM	charities
				WHERE	charity_id = ?';
		$query = $this->db->query($sql,array($charity_id));
		$row = $query->row();
		return $row->charity_article_id;
	}
	
	/**
	 * gets charity information
	 * @return charity name, article id, target_text, target, current
	 * @pre charity with id = charity_id exists
	 */
	function GetCharity($charity_id)
	{
		$sql = 'SELECT	charity_name,
						charity_article_id,
						charity_goal_text,
						charity_goal
				FROM	charities
				WHERE	charity_id = ?';
		$query = $this->db->query($sql,array($charity_id));
		$row = $query->row();
		return array(
			'name'=>$row->charity_name,
			'article'=>$row->charity_article_id,
			'target_text'=>$row->charity_goal_text,
			'target'=>$row->charity_goal);
	}
	
	/**
	 * retrieves the current charities id.
	 * @return the id of the current charity or false if none
	 */
	function GetCurrentCharity()
	{
		$sql = 'SELECT	charity_id
				FROM	charities
				WHERE	charity_current = 1
				AND		charity_deleted = 0
				LIMIT 	0,1';
		$query = $this->db->query($sql);
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->charity_id;
		}
		return FALSE;
	}
	
        /**
	 * retrieves a list of all charities.
	 */
	function GetCharities()
	{
		$sql = 'SELECT	charity_name,
				charity_id,
				charity_current
			FROM	charities
			WHERE	charity_deleted = 0
			ORDER BY charity_name ASC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[] = array(
					'id'=>$row->charity_id,
					'name'=>$row->charity_name,
					'iscurrent'=>$row->charity_current
					);
			}
		}
		return $result;
	}

        /**
	 * Adds a new charity to the database.
	 * @param $name the name of the charity to add
	 */
	function CreateCharity($name, $article_id)
	{
		$sql = 'INSERT INTO charities (
				charity_name,
				charity_article_id,
				charity_current)
			VALUES	(?, ?, FALSE)';
		$this->db->query($sql,array($name, $article_id));
	}

        /**
	 * Updates the given charity.
	 * @param $id the id of the charity
	 * @param $name the name of the charity
	 * @param $goal the target goal ammount
	 * @param $goaltext a description of what the charity is aiming for
	 */
	function UpdateCharity($id, $name, $goal, $goaltext)
	{
		$sql = 'UPDATE 	charities
			SET	charity_name = ?,
				charity_goal = ?,
				charity_goal_text = ?
			WHERE	charity_id = ?';
		$this->db->query($sql,array($name, $goal, $goaltext, $id));
	}

	/**
	 * Sets the charity with id to the current one.
	 * @param $id the id of the charity
	 * @return - true, if sucessfully set new charity
	 * @return - false, if charity that is trying to be set has no live content
	 */
	function SetCharityCurrent($id)
	{
		$this->db->trans_start();
			//see if charity has a published article
			$sql = 'SELECT 	articles.article_live_content_id
					FROM	charities
					JOIN	articles
					ON		articles.article_id = charities.charity_article_id
					WHERE	charities.charity_id = ?
					AND		articles.article_live_content_id IS NOT NULL';
			$query = $this->db->query($sql,array($id));
			if ($query->num_rows() == 1)
			{
				//set all charities to non current
				$sql = 'UPDATE 	charities
						SET		charity_current = 0';
				$this->db->query($sql);
				//set the new current charity
				$sql = 'UPDATE 	charities
						SET		charity_current = 1
						WHERE	charity_id = ?';
				$this->db->query($sql,array($id));
				$return = TRUE;
			}
			else
			{
				$return = FALSE;
			}
		$this->db->trans_complete();
		return $return;
	}

	/**
	 * Unsets the current charity
	 * @param $id the id of the charity
	 */
	function RemoveCharityAsCurrent($id)
	{
		$sql = 'UPDATE 	charities
				SET		charity_current = 0
				WHERE	charity_id = ?';
		$this->db->query($sql,array($id));
		return TRUE;
	}

        /**
	 * Deletes the charity with id.
	 * @param $id the id of the charity
	 */
	function DeleteCharity($id)
	{
		//set the charity to deleted
		$sql = 'UPDATE 	charities
			SET	charity_deleted = 1,
				charity_current = 0
			WHERE	charity_id = ?';
		$this->db->query($sql,array($id));
		$this->db->trans_complete();
	}
}