<?php
/**
* Stats Model for usefull feedback on members etc in the office
* @author Owen Jones
*
**/
class Stats_Model extends Model
{
	function Stats_Model()
	{
		parent::Model();
	}
	
	/////////////////////
	//USER STATS
	/////////////////////
	
	//@Returns an array (`number_of_members`,`confirmed_members`,`members_with_stats`)
	function NumberOfMembers()
	{
		$sql='
		SELECT COUNT(*) as number_of_members, 
		(
			SELECT COUNT(*) 
			FROM users 
			INNER JOIN entities ON
			entities.entity_id = users.user_entity_id
			WHERE entities.entity_deleted=0 AND entities.entity_password IS NOT NULL
		) as confirmed_members,
		(
			SELECT COUNT(*) 
			FROM users 
			INNER JOIN entities ON
			entities.entity_id = users.user_entity_id
			WHERE entities.entity_deleted=0 AND entities.entity_password IS NOT NULL AND users.user_gender IS NOT NULL
		) as members_with_stats
		FROM users 
		INNER JOIN entities ON
		entities.entity_id = users.user_entity_id
		WHERE entities.entity_deleted=0
		';
		$query = $this->db->query($sql);
		return $query->row_array();
	}
	
	//@Returns an array (`male`,`female`) 
	//Ignores unconfirmed members
	function GetMembersGenders()
	{
		$sql='
		SELECT COUNT(*) as male, 
		(
			SELECT COUNT(*) 
			FROM users 
			INNER JOIN entities ON
			entities.entity_id = users.user_entity_id
			WHERE entities.entity_deleted=0 AND users.user_gender=?
		) as female
		FROM users 
		INNER JOIN entities ON
		entities.entity_id = users.user_entity_id
		WHERE entities.entity_deleted=0 AND users.user_gender=?
		';
		$query = $this->db->query($sql,array('f','m'));
		return $query->row_array();
	}
	
	//@Returns an array of colleges with (`college_name`,`college_id`,`member_count`)
	function GetMembersColleges()
	{
		$sql='
		SELECT 
			DISTINCT(users.user_college_organisation_entity_id) as college_id,
			organisations.organisation_name as college_name,
			(
				SELECT 
				COUNT(*)
				FROM users
				INNER JOIN entities ON
				entities.entity_id = users.user_entity_id
				WHERE user_college_organisation_entity_id = college_id
				AND entities.entity_deleted=0
			) as member_count
		FROM users
		INNER JOIN organisations ON
			organisations.organisation_entity_id = users.user_college_organisation_entity_id
		WHERE users.user_college_organisation_entity_id IS NOT NULL
		ORDER BY college_name ASC
		';
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	//@Returns an array of years with (`enrollment_year`,`member_count`)
	function GetMembersEnrollmentYears()
	{
		$sql='
		SELECT 
			DISTINCT(users.user_enrolled_year) as enrollment_year,
			(
				SELECT 
				COUNT(*)
				FROM users
				INNER JOIN entities ON
				entities.entity_id = users.user_entity_id
				WHERE user_enrolled_year = enrollment_year
				AND entities.entity_deleted=0
			) as member_count
		FROM users
		WHERE users.user_enrolled_year IS NOT NULL
		ORDER BY enrollment_year ASC
		';
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	//@Returns an array (`12_hour`,`24_hour`)
	function GetMembersTimeFormats()
	{
		$sql='
		SELECT COUNT(*) as 24_hour, 
		(
			SELECT COUNT(*) 
			FROM users 
			INNER JOIN entities ON
			entities.entity_id = users.user_entity_id
			WHERE entities.entity_deleted=0 AND users.user_time_format=?
		) as 12_hour
		FROM users 
		INNER JOIN entities ON
		entities.entity_id = users.user_entity_id
		WHERE entities.entity_deleted=0 AND users.user_time_format=?
		';
		$query = $this->db->query($sql,array('12','24'));
		return $query->row_array();
	}
	
	//@Returns an two percentages of links (`official`,`unofficial`)
	function GetLinksPercentages()
	{
		$sql='
			SELECT COUNT(*) as official, 
			(
				SELECT COUNT(*)
				FROM user_links
				INNER JOIN links ON
						user_links.user_link_link_id = links.link_id
				WHERE links.link_official = 0
			) as unofficial
			FROM user_links
			INNER JOIN links ON
					user_links.user_link_link_id = links.link_id
			WHERE links.link_official = 1';
		$query = $this->db->query($sql);
		$data = $query->row_array();
		$official = round(($data['official'] / ($data['official']+$data['unofficial']))*100);
		return array(
						'official' => $official,
						'unofficial' => 100 - $official
					);
	}
	function GetNumberOfUsersWithLinksByGroups($max=6)
	{
		$sql='SELECT 
				user_links.user_link_user_entity_id,
				COUNT(*) as user_links_count 
			FROM user_links 
			INNER JOIN users ON
				user_links.user_link_user_entity_id = users.user_entity_id
			INNER JOIN entities ON
			entities.entity_id = users.user_entity_id 
			WHERE entities.entity_deleted=0 
			GROUP BY user_link_user_entity_id';
		$query = $this->db->query($sql);
		//Create an array of buckets for each count 0 - $max
		$links_array = array_fill(0, $max+1, 0);
		foreach($query->result() as $user)
		{
			if ($user->user_links_count > $max) {
				$links_array[$max]++;
			} else {
				$links_array[$user->user_links_count]++;
			}
		}
		return $links_array;
	}
	//$level can be either 'office' 'admin' 'vip'
	//If left blank it will return the number of members with any special access
	//Returns an array of numbers (`office`,`admin`,`vip`)
	function GetNumberOfMembersWithAccess()
	{
	$sql='
		SELECT COUNT(*) as office,
		(
			SELECT COUNT(*)
			FROM users 
			INNER JOIN entities ON
				entities.entity_id = users.user_entity_id
			WHERE entities.entity_deleted=0 
			AND entities.entity_password IS NOT NULL
			AND users.user_admin = 1
		) as admin,
		(
			SELECT COUNT(*)
			FROM subscriptions 
			WHERE subscriptions.subscription_deleted=0 
			AND subscriptions.subscription_vip_status=?
		) as vip
		FROM users 
		INNER JOIN entities ON
			entities.entity_id = users.user_entity_id
		WHERE entities.entity_deleted=0 
		AND entities.entity_password IS NOT NULL
		AND users.user_office_access = 1';
		$query = $this->db->query($sql,array('approved'));
		return $query->row_array();
	}
	//Get number of registered users between the two timestamps
	//If left blank will get all users up to the current time
	//@returns an int
	function GetNumberOfSignUps($start_timestamp='0000-00-00 00:00:00', $end_timestamp='9999-99-99 99:99:99')
	{
	$sql='
		SELECT COUNT(*) as signups
		FROM users 
		INNER JOIN entities ON
			entities.entity_id = users.user_entity_id
		WHERE entities.entity_deleted=0 
		AND entities.entity_password IS NOT NULL
		AND users.user_timestamp > ?
		AND users.user_timestamp < ?';
		$query = $this->db->query($sql,array($start_timestamp,$end_timestamp));
		if ($query->num_rows()>0){
			return $query->row()->signups;
		}else{
			return 0;
		}
	}
	
	//Warning to avoid biased results in the last element make sure the $ay_spacing is a  factor of $days_ago
	//Only change days ago if you want to limit the array size, eg you want to know over the last 365 days.
	function GetCumulativeSignUpsArrayOverLastDays($days_ago=30,$day_spacing=1)
	{
		$timestamp = date('Y-m-d 00:00:00', time() - (86400 * $days_ago));
		$sql='
		SELECT 
			UNIX_TIMESTAMP(users.user_timestamp) as time_joined
		FROM users 
		INNER JOIN entities ON
			entities.entity_id = users.user_entity_id
		WHERE entities.entity_deleted=0 
		AND entities.entity_password IS NOT NULL
		AND users.user_timestamp > ?';
		$query = $this->db->query($sql,array($timestamp));
		
		$base_count = self::GetNumberOfSignUps('0000-00-00 00:00:00', $timestamp);
		
		//Fill a result array with enough spaces ($days_ago/$day_spacing)
		//Pre fill them with the base level
		$num_of_day_groups = ceil($days_ago/$day_spacing);
		$days_array = array_fill(0, $num_of_day_groups, $base_count);
		foreach($query->result() as $row)
		{
			//This is a value of how many days ago this signup happened
			$day_group = floor((time() - $row->time_joined) / (86400 * $day_spacing));
			//Add one for each day before and equal to this date
			//Position 0 is $days_ago Days Ago
			//Last Position is Today
			for ($i=$day_group;$i<$num_of_day_groups;$i++) {
				$days_array[$i]++;
			}
		}
		return $days_array;
	}
	
	/////////////////////////
	//SUBSCRIPTIONS
	////////////////////////
	//@input int limit for results
	//@returns an array of organisations by the number of subscriptions (`organisation_id`,`organisation_name`,`subscription_count`)
	function GetTopSubscribedOrgs($limit=10)
	{
		$sql='
		SELECT 
			DISTINCT(subscriptions.subscription_organisation_entity_id) as organisation_id,
			organisations.organisation_name as organisation_name,
			(
				SELECT 
				COUNT(*)
				FROM subscriptions
				WHERE subscriptions.subscription_organisation_entity_id = organisation_id
				AND subscriptions.subscription_deleted=0
			) as subscription_count
		FROM subscriptions
		INNER JOIN organisations ON
			organisations.organisation_entity_id = subscriptions.subscription_organisation_entity_id
		WHERE subscriptions.subscription_deleted=0
		ORDER BY subscription_count DESC LIMIT ?
		';
		$query = $this->db->query($sql,array($limit));
		return $query->result_array();
	}

	//@input int limit for results
	//@returns the average number of subscriptions per user, not rounded!
	function GetAverageNumberOfSubscriptions()
	{
	$members = self::NumberOfMembers();
	$sql='
		SELECT COUNT(*) as total_subscriptions
		FROM subscriptions
		WHERE subscriptions.subscription_deleted=0
		';
		$query = $this->db->query($sql);
		$total = $query->row_array();
		return ($total['total_subscriptions'] / $members['confirmed_members']);
	}
	
	//@input int limit for results
	//@returns an array of organisations by the time of the last person subscribing (`organisation_id`,`organisation_name`,`last_joined`)
	function MostRecentlySubscribedOrganisations($limit=10)
	{
		$sql='
		SELECT 
			subscriptions.subscription_organisation_entity_id as organisation_id,
			UNIX_TIMESTAMP(MAX(subscriptions.subscription_timestamp)) as last_joined,
			organisations.organisation_name as organisation_name
		FROM subscriptions
		INNER JOIN organisations ON
			organisations.organisation_entity_id = subscriptions.subscription_organisation_entity_id
		WHERE subscriptions.subscription_deleted=0
		GROUP BY subscription_organisation_entity_id
		ORDER BY last_joined DESC LIMIT ?
		';
		$query = $this->db->query($sql,array($limit));
		return $query->result_array();
	}
	
	//////////////////////////////
	//COMMENTS
	//////////////////////////////
	
	//@returns an array of size limit of (`id`,`firstname`,`surname`,`email`,`total_post_count`,`anonymous_post_count`,`deleted_post_count`)
	//This ignores deleted and anonomous posts
	function GetTopCommentingUsers($limit=10)
	{
		$sql='
		SELECT 
			users.user_entity_id as id,
			users.user_firstname as firstname,
			users.user_surname as surname,
			users.user_email as email,
			COUNT(*) as total_post_count,
			(
				SELECT COUNT(*)
				FROM comments
				WHERE comments.comment_deleted = 1
				AND comments.comment_anonymous = 0
				AND comments.comment_author_entity_id = users.user_entity_id
			) as deleted_post_count
		FROM comments
		INNER JOIN users ON
		comments.comment_author_entity_id = users.user_entity_id
		WHERE comments.comment_deleted = 0
		AND comments.comment_anonymous = 0
		GROUP BY comment_author_entity_id
		ORDER BY total_post_count DESC LIMIT ?
		';
		$query = $this->db->query($sql,array($limit));
		return $query->result_array();
	}
	//Returns a list of the users with the most deleted comments (excludes annon posts)
	//@returns an array of size limit of (`id`,`firstname`,`surname`,`email`,`total_post_count`,`anonymous_deleted_post_count`,`deleted_post_count`)
	function GetTopDeletedCommentingUsers($limit=10)
	{
		$sql='
		SELECT 
			users.user_entity_id as id,
			users.user_firstname as firstname,
			users.user_surname as surname,
			users.user_email as email,
			COUNT(*) as total_post_count,
			(
				SELECT COUNT(*)
				FROM comments
				WHERE comments.comment_deleted = 1
				AND comments.comment_anonymous = 0
				AND comments.comment_author_entity_id = users.user_entity_id
			) as deleted_post_count
		FROM comments
		INNER JOIN users ON
		comments.comment_author_entity_id = users.user_entity_id
		WHERE comments.comment_deleted = 0
		AND comments.comment_anonymous = 0
		GROUP BY comment_author_entity_id
		ORDER BY deleted_post_count DESC LIMIT ?
		';
		$query = $this->db->query($sql,array($limit));
		return $query->result_array();
	}
	
	//This function gets an array of articles ordered by the most comments (includes deleted and annon comments)
	//@Returns (`article_id`,`article_title`,`section_id`,`section_codename`,`section_name`,`parent_section_id`,`parent_section_codename`,`parent_section_name`,`comment_count`)
	//@Note includes the parent section details for example an article might be from section football and parent section sport
	function GetTopCommentedArticles($limit=10)
	{
		$sql='
		SELECT 
			articles.article_id as article_id,
			article_contents.article_content_heading as article_title,
			content_types.content_type_id as section_id,
			content_types.content_type_codename as section_codename,
			content_types.content_type_name as section_name,
			parent_types.content_type_id as parent_section_id,
			parent_types.content_type_codename as parent_section_codename,
			parent_types.content_type_name as parent_section_name,
			(
				SELECT COUNT(*)
				FROM comments
				WHERE comments.comment_comment_thread_id = articles.article_public_comment_thread_id
				AND comments.comment_deleted=1
			) as deleted_post_count,
			(
				SELECT COUNT(*)
				FROM comments
				WHERE comments.comment_comment_thread_id = articles.article_public_comment_thread_id
			) as total_post_count
		FROM articles
		INNER JOIN article_contents ON
			articles.article_live_content_id = article_contents.article_content_id
		INNER JOIN content_types ON
			articles.article_content_type_id = content_types.content_type_id
		LEFT JOIN content_types as parent_types ON
			content_types.content_type_parent_content_type_id = parent_types.content_type_id
		ORDER BY total_post_count DESC LIMIT ?';
		$query = $this->db->query($sql,array($limit));
		return $query->result_array();
	}
	
	//Warning to avoid biased results in the last element make sure the $ay_spacing is a  factor of $days_ago
	//Only change days ago if you want to limit the array size, eg you want to know over the last 365 days.
	function GetCumulativeCommentsArrayOverLastDays($days_ago=30,$day_spacing=1)
	{
		$timestamp = date('Y-m-d 00:00:00', time() - (86400 * $days_ago));
		//Get the number of comments before timestamp
		$sql='SELECT COUNT(*) as comment_count
		FROM comments 
		WHERE comments.comment_deleted=0
		AND comments.comment_post_time <= ?';
		$query = $this->db->query($sql,array($timestamp));
		$base_count = $query->row()->comment_count;
		
		//Get the array of comment times since timestamp
		$sql='SELECT UNIX_TIMESTAMP(comments.comment_post_time) as post_time
		FROM comments 
		WHERE comments.comment_deleted=0
		AND comments.comment_post_time > ?';
		$query = $this->db->query($sql,array($timestamp));
		
		//Fill a result array with enough spaces ($days_ago/$day_spacing)
		//Pre fill them with the base level
		$num_of_day_groups = ceil($days_ago/$day_spacing);
		$days_array = array_fill(0, $num_of_day_groups, $base_count);
		foreach($query->result() as $row)
		{
			//This is a value of how many days ago this signup happened
			$day_group = floor((time() - $row->post_time) / (86400 * $day_spacing));
			//Add one for each day before and equal to this date
			//Position 0 is $days_ago Days Ago
			//Last Position is Today
			for ($i=$day_group;$i<$num_of_day_groups;$i++) {
				$days_array[$i]++;
			}
		}
		return $days_array;
	}
}
?>