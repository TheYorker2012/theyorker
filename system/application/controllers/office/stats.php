<?php
/**
* Stats Controller for usefull feedback on members etc in the office
* @author Owen Jones
*
**/
class Stats extends controller
{
	function __construct()
	{
		//Load model for all pages
		parent::controller();
		$this->load->model('stats_model');
	}
	function index ()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('editor')) return;
		
		//Get Page Properties
		$this->pages_model->SetPageCode('office_stats');
		$data = array();
		$data['page_information'] = $this->pages_model->GetPropertyWikitext('page_information');
		
		
		/////////////Get Information From Database
		//users
		$data['members'] = $this->stats_model->NumberOfMembers();//(`number_of_members`,`confirmed_members`,`members_with_stats`)
		$data['member_genders'] = $this->stats_model->GetMembersGenders();//(`male`,`female`)
		$data['member_colleges'] = $this->stats_model->GetMembersColleges();//array of(`college_name`,`college_id`,`member_count`)
		$data['member_enrollments'] = $this->stats_model->GetMembersEnrollmentYears();//array of (`enrollment_year`,`member_count`)
		$data['member_times'] = $this->stats_model->GetMembersTimeFormats();//(`12_hour`,`24_hour`)
		$data['member_links'] = $this->stats_model->GetAverageNumberOfUserLinks();//(`average`,`average_official`,`average_unofficial`)
		//subscriptions
		$data['subscription_average'] = round($this->stats_model->GetAverageNumberOfSubscriptions());//float
		$data['most_subscribed_orgs'] = $this->stats_model->GetTopSubscribedOrgs(10);// (`organisation_id`,`organisation_name`,`subscription_count`)
		$data['recent_subscribed_orgs'] = $this->stats_model->MostRecentlySubscribedOrganisations(5);//(`organisation_id`, `organisation_name`, `last_joined`)
		//comments
		$data['comment_top_users'] = $this->stats_model->GetTopCommentingUsers(10);
		$data['comment_deleted_users'] = $this->stats_model->GetTopDeletedCommentingUsers(10);
		$data['comment_top_articles'] = $this->stats_model->GetTopCommentedArticles(10);
		
		//access levels
		$data['access'] = $this->stats_model->GetNumberOfMembersWithAccess();
		//signups
		$data['registrations'] = 
		array(
			//in the last 24 hours (86400 seconds)
			'day'			=> $this->stats_model->GetNumberOfSignUps(date('Y-m-d H:i:s',time()-86400)),
			//in the last week (604800 seconds)
			'week'			=> $this->stats_model->GetNumberOfSignUps(date('Y-m-d H:i:s',time()-604800)),
			//so far this month
			'month'			=> $this->stats_model->GetNumberOfSignUps(date('Y-m-00').' 00:00:00'),
			//so far this year
			'year'			=> $this->stats_model->GetNumberOfSignUps(date('Y-00-00').' 00:00:00'),
			//so far this accademic year (start of october)
			'academic_year'	=> $this->stats_model->GetNumberOfSignUps(date('Y-10-00').' 00:00:00')
		);
		
		//Load view and send data
		$this->main_frame->SetContentSimple('office/stats/stats', $data);
		$this->main_frame->IncludeCss('/stylesheets/campaign.css');//Use this for making nice bar charts
		$this->main_frame->Load();
	}
}
?>