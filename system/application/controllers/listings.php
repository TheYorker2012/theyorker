<?php

class Listings extends Controller {

	function Listings()
	{
		parent::Controller();	
	}
	
	// default function
	function index()
	{
		// Sorry about the clutter, this will be moved in a bit but it isn't
		// practical to put it in the view
		$extra_head = <<<EXTRAHEAD
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script type="text/javascript">
			var myEvents=new Array();
			var currentMenuSubject;
			function eventMenu(event) {
				//currentMenuSubject = event.srcElement.id;
				if (1) { //($(Event.element(event)) == 'ev_1') {
					var xPos = Event.pointerX(event);
					var yPos = Event.pointerY(event);
					$('calviewEventMenu').style.top=yPos;
					$('calviewEventMenu').style.left=xPos;
					new Effect.Appear ('calviewEventMenu', {duration:0.2});
				}	
			}
			function eventSetHighlight (e) {
				//$('ev_1').class="indEventBoxHL";
				$(currentMenuSubject).style.color="#ff0000";
			}
			function hideEventMenu (e) {
				new Effect.Fade ('calviewEventMenu',{duration:0.2});
			}
			function eventCreate(date,time,sid,title,loc,blurb) {
				
			}
			function hideEvent (idn) {
				new Effect.Puff ('ev_'+idn,{duration:0.5});
			}
			function expandEvent (idn) {
				new Effect.Appear ('ev_es_'+idn,{duration:0.2});
			}
			function collapseEvent (idn) {
				new Effect.Fade ('ev_es_'+idn,{duration:0.2});
			}
			</script>
EXTRAHEAD;
		
		
		// Load my "minitemplater" helper.
		// This is a very basic S&R script
		// : Allows chunks of template code to be parsed without cluttering
		// up the script :)
		$this->load->helper('minitemplater');
		
		// This array get sent to the view listings_view.php
		$data = Array ();
		
		// I don't trust users to set their clocks properly
		$data['server_dt'] = time(); 
		// Set title and other such
		$data['title'] = 'Listing viewer prototype';
		
		// this is temporary for testing only
		$data['days'] = array ();
		$daycalc = array ();
		for ($dayoffset = 0; $dayoffset < 7; $dayoffset++) {
			$dayofweek = date('N',time ()) - 1;
			
			$monday = strtotime ('-'.$dayofweek." day",time());
			
			$day_ts = strtotime ('+'.$dayoffset." day",$monday);
			
			$data['days'][]	= date ("jS M", $day_ts);
			$daycalc[] = date ('d#m#y',$day_ts);
		}
		
		// returns the day of the week that a date falls on if
		// that date is within the range of the current calendar

		
		// define some dummy events with a rough schema until we have access
		// to some real data to play with
		$data['dummies'] = array (
			array (
				'ref_id' => '1',
				'name' => 'House Party',
				'date' => '2006-12-4',
				'day' => $this->get_dow_offset ('2006-12-4',$daycalc),
				'starttime' => '2100',
				'endtime' => '0000',
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social'
			),
			array (
				'ref_id' => '2',
				'name' => 'boring lecture about vegetables',
				'date' => '2006-12-8',
				'day' => $this->get_dow_offset ('2006-12-8',$daycalc),
				'starttime' => '1245',
				'endtime' => '1500',
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'academic'
			)
		);
		
		
		
		$pass_data['subdata'] = $data;
		$pass_data['extra_head'] = $extra_head;
		$pass_data['content_view'] = "listings/listings";
		// load crazy frame deely		
		$this->load->view('frames/StudentFrameCss',$pass_data);
		
		//$this->load->view('listings_view',$data);
	}
	
	function get_dow_offset ($date,$daycalci) {
		$ts = strtotime ($date);
		foreach ($daycalci as $os => $date) {
			if (date ('d#m#y',$ts) == $date) {
				return $os;
				break;
			}
		}
		return -1;
		break;
	}
}
?>