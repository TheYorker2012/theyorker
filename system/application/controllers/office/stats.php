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
	//Creates Google Charts Extended Codes for Numbers between $min - $min
	//This function scales the numbers then converts them into a code for the url
	private function googlechart_extended_encode($numbers,$min,$max)
	{
		$encoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';
		$result_string='';
		foreach ($numbers as $number) {
			//Number needs to be translated to a relative position to the maximum number storable ($min+4095)
			$range = $max-$min;
			if($range==0){
				$rel_number = 0;//Avoid division by zero
			}else{
				$rel_number = round((($number-$min) / ($range))*4095);
			}
			$first = floor($rel_number / 64);
			$second = $rel_number % 64;
			$result_string .= $encoding[$first].$encoding[$second];
		}
		return $result_string;
	}
	//this is for increasing data
	private function simple_google_dayslinechart($title,$data_array,$x_size,$y_size,$days,$axis_range_rounding)
	{
		//Create Google Charts Line Chart showing change over $days Days
		$chart_min = floor($data_array[0]/$axis_range_rounding)*$axis_range_rounding;//go to the previous $axis_range_rounding multiple
		$chart_max = ceil(end($data_array)/$axis_range_rounding)*$axis_range_rounding;//go to the next $axis_range_rounding multiple
		//If there is no variation in the dataset keep a range of axis range rounding
		if($chart_min==$chart_max){
			$chart_min = $chart_min - floor($axis_range_rounding/2);
			$chart_max = $chart_max + ceil($axis_range_rounding/2);
		}
		//Encode the days, they are already in the right format of oldest first
		$encoded_days = $this->googlechart_extended_encode($data_array,$chart_min,$chart_max);
		$url  ='http://chart.apis.google.com/chart?';
		$url .='chtt='.$title;//Chart Title
		$url .='&chs='.$x_size.'x'.$y_size;//Chart size
		$url .='&cht=lc';//Chart type (Line Chart)
		$url .='&chxt=x,y';//Include Y and X axis
		$url .='&chxr=0,0,'.$days.'|1,'.$chart_min.','.$chart_max;//Chart Ranges
		$url .='&chxl=0:|'.$days.'+days+ago|Today';//Chart Labels
		$url .='&chd=e:'.$encoded_days;//encoded data
		return $url;
	}
	
	//Data array is an array of values. Must be in range 0-100
	private function simple_google_pie_chart($title,$data_array,$label_array,$x_size,$y_size)
	{
		//Create Google Charts PieChart
		$encoded_data = $this->googlechart_extended_encode($data_array,0,100);
		$url  ='http://chart.apis.google.com/chart?';
		$url .='chtt='.$title;//Chart Title
		$url .='&chs='.$x_size.'x'.$y_size;//Chart size
		$url .='&cht=p3';//Chart type (Pie Chart)
		$url .='&chd=e:'.$encoded_data;//encoded data
		$url .='&chl='.implode("|",$label_array);//Chart labels
		return $url;
	}
	
	private function simple_google_bar_chart($title,$data_array,$label_array,$x_size,$y_size,$axis_range_rounding)
	{
		$lowest = $data_array[0];
		$highest = $data_array[0];
		foreach($data_array as $data){
			if($data < $lowest) $lowest=$data;
			if($data > $highest) $highest=$data;
		}
		
		//Create Google Charts Bar Chart
		$chart_max = ceil($highest/$axis_range_rounding)*$axis_range_rounding;//go to the next $axis_range_rounding multiple
		$chart_min = floor($lowest/$axis_range_rounding)*$axis_range_rounding;//go to the previous $axis_range_rounding multiple
		
		$encoded_data = $this->googlechart_extended_encode($data_array,$chart_min,$chart_max);
		$url  ='http://chart.apis.google.com/chart?';
		$url .='chtt='.$title;//Chart Title
		$url .='&chs='.$x_size.'x'.$y_size;//Chart size
		$url .='&cht=bhs';//Chart type (Bar Chart)
		$url .='&chxt=x,y';
		$url .='&chd=e:'.$encoded_data;//encoded data
		$url .='&chxr=0,'.$chart_min.','.$chart_max;//Chart Ranges
		$url .='&chxl=1:|'.implode("|",$label_array);//Chart labels
		return $url;
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
		//subscriptions
		$data['subscription_average'] = round($this->stats_model->GetAverageNumberOfSubscriptions());//float
		$data['most_subscribed_orgs'] = $this->stats_model->GetTopSubscribedOrgs(10);// (`organisation_id`,`organisation_name`,`subscription_count`)
		$data['recent_subscribed_orgs'] = $this->stats_model->MostRecentlySubscribedOrganisations(5);//(`organisation_id`, `organisation_name`, `last_joined`)
		//comments
		$data['comment_top_users'] = $this->stats_model->GetTopCommentingUsers(10);
		$data['comment_deleted_users'] = $this->stats_model->GetTopDeletedCommentingUsers(10);
		$data['comment_top_articles'] = $this->stats_model->GetTopCommentedArticles(10);
		
		$data['members_who_have_posted'] = 
			round(($this->stats_model->NumberOfConfimedUsersWhoHavePosted(1) / $data['members']['confirmed_members'])*100);
		$data['members_who_have_posted_5m'] = 
			round(($this->stats_model->NumberOfConfimedUsersWhoHavePosted(5) / $data['members']['confirmed_members'])*100);
		
		//access levels
		//@note this is being left out untill its improved, the figures like people with admin access isnt used and is confusing.
		//$data['access'] = $this->stats_model->GetNumberOfMembersWithAccess();
		
		//signups
		if((int)date('m')<10){
			//its before the start of a new accademic year so use last year
			$acc_year = (int)date('Y') - 1;
		}else{
			//its after the start of a new accademic year, use this year.
			$acc_year = date('Y');
		}
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
			'academic_year'	=> $this->stats_model->GetNumberOfSignUps($acc_year.'-10-00 00:00:00')
		);
		
		///////Create Graph of Registrations over the last X days.
		$days=30;
		$registrations = $this->stats_model->GetCumulativeSignUpsArrayOverLastDays($days,2);
		$data['signups_img_url'] = $this->simple_google_dayslinechart('Total+Registrations',$registrations,200,125,$days,10);
		
		//Create graph of Comments posted over the last X days.
		$days=30;
		$comments = $this->stats_model->GetCumulativeCommentsArrayOverLastDays($days,1);
		
		$data['comments_img_url'] = $this->simple_google_dayslinechart('Total+Comments+Posted',$comments,300,200,$days,10);
		
		
		$links_data = $this->stats_model->GetNumberOfUsersWithLinksByGroups(6);
		$links_labels = array('0','1','2','3','4','5','6 plus');
		$data['links_bar_chart_img'] = $this->simple_google_bar_chart('User+Link+Numbers',$links_data,$links_labels,200,240,10);
		
		$links_percent = $this->stats_model->GetLinksPercentages();
		$pie_data = array($links_percent['official'],$links_percent['unofficial']);
		$pie_labels = array('Official','Unofficial');
		$data['official_links_pie_img'] = $this->simple_google_pie_chart('Link+Types',$pie_data,$pie_labels,200,90);
		
		//Load view and send data
		$this->main_frame->SetContentSimple('office/stats/stats', $data);
		$this->main_frame->IncludeCss('/stylesheets/campaign.css');//Use this for making nice bar charts
		$this->main_frame->Load();
	}
}
?>