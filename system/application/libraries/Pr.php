<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file PR.php
 * @brief Library for getting pr rating information for organisations.
 * @author Richard Ingle (ri504@cs.york.ac.uk)
 */

class Pr
{
	/// Code igniter instance.
	private $CI;

	/// Default constructor
	function __construct()
	{
		$this->CI = &get_instance();
		//load the required models
		$this->CI->load->model('pr_model');
		$this->CI->load->model('review_model');
		$this->CI->load->model('directory_model');
		$this->CI->load->library('organisations','organisations');
		$this->CI->load->model('slideshow','slideshow');
	}
	
	//returns the current organisation versus total organisation score
	//$scores is what is returned by GetOrganisationScores
	function GetOrganisationTotalScore($dir_entry_name, $scores)
	{
		//get the organisations directory information
		$org_data = $this->CI->organisations->_GetOrgData($dir_entry_name);
		//initialise
		$result['score_current'] = 0;
		$result['score_possible'] = 0;
		foreach ($scores as $key => $entry)
		{
			switch ($key)
			{
				case 'calendar':
					if (isset($entry['body']))
					{
						foreach ($entry['body'] as $row)
						{
							//max of 1 mark for events
							$result['score_current'] += min($row['score_current'], 1);
							$result['score_possible'] += 1;
						}
					}
					break;
				case 'deals':
					break;
				case 'information':
					if (isset($entry['body']))
					{
						foreach ($entry['body'] as $row)
						{
							$result['score_current'] += $row['score_current'];
							$result['score_possible'] += $row['score_possible'];
						}
					}
					break;
				case 'photos':
					if (isset($entry['body']))
					{
						foreach ($entry['body'] as $row)
						{
							//max of 3 marks for photos
							$result['score_current'] += min($row['score_current'], 3);
							$result['score_possible'] += 3;
						}
					}
					break;
				case 'tags':
					if (isset($entry['body']))
					{
						foreach ($entry['body'] as $row)
						{
							//max of 1 mark for a tag group
							$result['score_current'] += min($row['score_current'], 1); //can be 0 = no tags, or 1 = 1 or more tags
							$result['score_possible'] += 1;
						}
					}
					break;
				case 'other':
					if (isset($entry['body']))
					{
						foreach ($entry['body'] as $row)
						{
							//max of 3 marks for societies, 1 mark for others
							if ($org_data['organisation']['type_codename'] == 'societies')
								$max_score = 3;
							else
								$max_score = 1;
							$result['score_current'] += min($row['score_current'], $max_score);
							$result['score_possible'] += $max_score;
						}
					}
					break;
			}
		}
		return($result);
	}
	
	//scores-category-item
	function GetOrganisationScores($dir_entry_name)
	{	
		//get the organisations directory information
		$org_data = $this->CI->organisations->_GetOrgData($dir_entry_name);
		
		$scores['calendar'] = self::GetOrganisationScoresCalender($dir_entry_name);
		
		//$scores['deals'] = self::GetOrganisationScoresDeals($dir_entry_name);
		
		$scores['information'] = self::GetOrganisationScoresInformation($dir_entry_name, $org_data);
		
		$scores['photos'] = self::GetOrganisationScoresPhotos($dir_entry_name, $org_data);
		
		$scores['tags'] = self::GetOrganisationScoresTags($dir_entry_name);
		
		$scores['other'] = self::GetOrganisationScoresOther($dir_entry_name, $org_data);
		
		return $scores;
	}
	
	function GetOrganisationScoresCalender($dir_entry_name)
	{
		//get calender head
		$calendar['head']['name'] = 'Calender';
		//calender - events
		
		// Event statistics
		$this->CI->load->library('calendar_backend');
		$this->CI->load->library('calendar_source_yorker');
		$yorker_source = new CalendarSourceYorker(0);
		// Only those events of the organisation
		$yorker_source->DisableGroup('subscribed');
		$yorker_source->DisableGroup('owned');
		$yorker_source->DisableGroup('private');
		$yorker_source->EnableGroup('active');
		$yorker_source->DisableGroup('inactive');
		$yorker_source->EnableGroup('show');
		$yorker_source->EnableGroup('rsvp');
		$yorker_source->IncludeStream((int)$this->CI->pr_model->GetOrganisationID($dir_entry_name), TRUE);
		
		//times for the next 2 weeks
		$last_monday = strtotime("last Monday");
		$next_monday = strtotime("next Monday");
		$week_monday = strtotime("+1 week", $next_monday);
		
		//set time to this week
		$yorker_source->SetRange($last_monday, $next_monday);
		$this_result = $yorker_source->MainQuery( 'COUNT(*)', $yorker_source->ProduceWhereClause());
		
		//set time to this week
		$yorker_source->SetRange($next_monday, $week_monday);
		$next_result = $yorker_source->MainQuery( 'COUNT(*)', $yorker_source->ProduceWhereClause());		
		
		//$this->CI->messages->AddDumpMessage('this_result', $this_result);
		//$this->CI->messages->AddDumpMessage('next_result', $next_result);
		
		//result = $yorker_source->MainQuery( 'COUNT(event_occurrences.event_occurrence_id)', $yorker_source->ProduceWhereClause().' GROUP BY organisations.organisation_entity_id' 	);
		
		$score_item['name'] = 'Events - This Week';
		$score_item['link'] = '#';
		$score_item['score_current'] = $this_result[0]['COUNT(*)'];
		$score_item['score_possible'] = 1;
		$calendar['body'][] = $score_item;
		
		$score_item['name'] = 'Events - Next Week';
		$score_item['link'] = '#';
		$score_item['score_current'] = $next_result[0]['COUNT(*)'];
		$score_item['score_possible'] = 1;
		$calendar['body'][] = $score_item;
		
		return $calendar;
	}
	
	function GetOrganisationScoresDeals($dir_entry_name)
	{		
		//get deal head
		$deals['head']['name'] = 'Deals';
		//deals - drink
		$deals['body']['0']['name'] = '*Drink';
		$deals['body']['0']['link'] = '#';
		$deals['body']['0']['score_current'] = '0';
		$deals['body']['0']['score_possible'] = '0';
		//deals - food
		$deals['body']['1']['name'] = '*Food';
		$deals['body']['1']['link'] = '#';
		$deals['body']['1']['score_current'] = '0';
		$deals['body']['1']['score_possible'] = '0';
		
		return $deals;
	}
	
	function GetOrganisationScoresInformation($dir_entry_name, $org_data)
	{		
		//get context type data
		$context_types = $this->CI->review_model->GetOrganisationReviewContextTypes($dir_entry_name);
		
		//get information head
		$information['head']['name'] = 'Information';
		
		//information - drink
		$score_item['name'] = 'Directory';
		$score_item['link'] = '/office/pr/org/'.$dir_entry_name.'/directory/information';
		$score_item['score_current'] = 0;
		$score_item['score_possible'] = 0;
		//check that the fields have data
		$score_item['score_possible']++;
		if ($org_data['organisation']['description'] != '')
		{
			$score_item['score_current']++;
		}
		$score_item['score_possible']++;
		if ($org_data['organisation']['email_address'] != '')
		{
			$score_item['score_current']++;
		}
		$score_item['score_possible']++;
		if ($org_data['organisation']['website'] != '')
		{
			$score_item['score_current']++;
		}
		$score_item['score_possible']++;
		if ($org_data['organisation']['postal_address'] != '')
		{
			$score_item['score_current']++;
		}
		$score_item['score_possible']++;
		if ($org_data['organisation']['postcode'] != '')
		{
			$score_item['score_current']++;
		}
		$score_item['score_possible']++;
		if ($org_data['organisation']['open_times'] != '')
		{
			$score_item['score_current']++;
		}
		$score_item['score_possible']++;
		if ($org_data['organisation']['phone_internal'] != '')
		{
			$score_item['score_current']++;
		}
		$score_item['score_possible']++;
		if ($org_data['organisation']['phone_external'] != '')
		{
			$score_item['score_current']++;
		}
		$score_item['score_possible']++;
		if ($org_data['organisation']['fax_number'] != '')
		{
			$score_item['score_current']++;
		}
		
		$information['body'][] = $score_item;
		
		//for every context type we need to get tag data
		foreach ($context_types as $context_type)
		{
			//does the context type exist for this organisation
			if ($context_type['deleted'] != NULL)
			{
				$score_item['name'] = $context_type['content_name'];
				$score_item['link'] = '/office/reviews/'.$dir_entry_name.'/'.$context_type['content_codename'];
				$score_item['score_current'] = 0;
				$score_item['score_possible'] = 0;
				
				//get the review context type
				$context_type_details = $this->CI->review_model->GetReviewContextContents($dir_entry_name, $context_type['content_codename']);
				
				//check that the fields exist and have data
				$score_item['score_possible']++;
				if (isset($context_type_details['content_blurb']) && $context_type_details['content_blurb'] != '')
					$score_item['score_current']++;
					
				$score_item['score_possible']++;
				if (isset($context_type_details['content_quote']) && $context_type_details['content_quote'] != '')
					$score_item['score_current']++;
					
				$score_item['score_possible']++;
				if (isset($context_type_details['recommended_item']) && $context_type_details['recommended_item'] != '')
					$score_item['score_current']++;
					
				$score_item['score_possible']++;
				if (isset($context_type_details['average_price']) && $context_type_details['average_price'] = 0)
					$score_item['score_current']++;
					
				$score_item['score_possible']++;
				if (isset($context_type_details['serving_times']) && $context_type_details['serving_times'] != '')
					$score_item['score_current']++;
				
				$information['body'][] = $score_item;
			}
		}
		
		return $information;
	}
	
	function GetOrganisationScoresPhotos($dir_entry_name, $org_data)
	{		
		//get context type data
		$context_types = $this->CI->review_model->GetOrganisationReviewContextTypes($dir_entry_name);
		
		//get org id
		$org_id = $this->CI->pr_model->GetOrganisationID($dir_entry_name);
		
		//get photos head
		$photos['head']['name'] = 'Photos';
		
		//photos - drink
		$score_item['name'] = 'Directory';
		$score_item['link'] = '/office/pr/org/'.$dir_entry_name.'/directory/photos';
		$score_item['score_current'] = count($org_data['organisation']['slideshow']);
		$score_item['score_possible'] = 3;
		$photos['body'][] = $score_item;
		
		//for every context type we need to get tag data
		foreach ($context_types as $context_type)
		{
			//does the context type exist for this organisation
			if ($context_type['deleted'] != NULL)
			{
				//get context type photo count
				$slideshow = $this->CI->slideshow->GetReviewPhotos($org_id, $context_type['content_codename'], true);
				$score_item['name'] = $context_type['content_name'];
				$score_item['link'] = '/office/reviews/'.$dir_entry_name.'/'.$context_type['content_codename'].'/photos';
				$score_item['score_current'] = count($slideshow);
				$score_item['score_possible'] = 3;
				$photos['body'][] = $score_item;
			}
		}
		
		return $photos;
	}
	
	function GetOrganisationScoresTags($dir_entry_name)
	{
		//get context type data
		$context_types = $this->CI->review_model->GetOrganisationReviewContextTypes($dir_entry_name);
		
		//get tags head
		$tags['head']['name'] = 'Tags';
		
		//for every context type we need to get tag data
		foreach ($context_types as $context_type)
		{
			//does the context type exist for this organisation
			if ($context_type['deleted'] != NULL)
			{
				//get the tags that are available to the organisation in the context type
				$possible_tags = $this->CI->review_model->GetTagWithoutOrganisation($context_type['content_name'], $dir_entry_name);
				//get the tags the organisation has in the context type
				$current_tags = $this->CI->review_model->GetTagOrganisation($context_type['content_name'], $dir_entry_name);
				
				//for each possible tag group get the number of tags
				foreach ($possible_tags['tag_group_names'] as $tag_group)
				{
					$score_item['name'] = $context_type['content_name'].' - '.$tag_group;
					$score_item['link'] = '/office/reviews/'.$dir_entry_name.'/'.$context_type['content_codename'].'/tags/';
					//how many are there?
					if (isset($current_tags[$tag_group]))
					{
						$score_item['score_current'] = count($current_tags[$tag_group]);
					}
					else
					{
						$score_item['score_current'] = 0;
					}
					//an organisation only needs one tag per category
					$score_item['score_possible'] = 1;
					$tags['body'][] = $score_item;
				}
			}
		}
		
		return $tags;
	}
	
	function GetOrganisationScoresOther($dir_entry_name, $org_data)
	{
		//get the business card groups
		$business_card_groups = $this->CI->directory_model->GetDirectoryOrganisationCardGroups($dir_entry_name);
	
		//get other head
		$other['head']['name'] = 'Other';
		//other - drink
		$score_item['name'] = 'Business Cards';
		$score_item['link'] = '/office/pr/org/'.$dir_entry_name.'/directory/contacts';
		$score_item['score_current'] = 0;
		if ($org_data['organisation']['type_codename'] == 'societies')
			$score_item['score_possible'] = 3;
		else
			$score_item['score_possible'] = 1;
		
		foreach ($business_card_groups as &$group)
		{
			$group['cards'] = $this->CI->directory_model->GetDirectoryOrganisationCardsByGroupId($group['business_card_group_id']);
			$score_item['score_current'] += count($group['cards']);
		}
		
		$other['body'][] = $score_item;
		
		
		
		return $other;
	}
}

?>