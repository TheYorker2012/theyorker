<?php

	
/*
	This needs to look like the right hand side of /directory/fragsoc/events as
	of revision 302 (see views/directory/directory_view_events at revision 302).
	The relevent html is here (if (0)'d out):
*/
if (0) {
?>
        	<h2>Week 5</h2>
            <h3>Wednesday 8th November</h3>
            <table width="630" style="border-width: 1; border-style:solid; border-color: #06a5cd;">
                <TR>
                    <TD width="130"> 6:15pm - 7:15pm </TD>
                    <TD width="296"><b>Rage Rally</b> </TD>
                    <TD width="20" align="center"><img src="/images/prototype/listings/flag.gif"> </TD>
                    <TD width="20" align="center"><img src="/images/prototype/listings/todo.gif"> </TD>
                    <TD width="47" align="center"><img src="/images/prototype/listings/rsvp.gif"> </TD>
                    <TD width="22" align="center"><img src="/images/prototype/listings/email.jpg" width="19" height="19"> </TD>
                    <TD width="22" align="center"><a href="http://www.theyorker.co.uk/"> <img src="/images/prototype/listings/link.png" border="0"> </a></TD>
                    <TD width="73" align="center"><img src="/images/prototype/listings/subscribe.jpg"></TD>
                </TR>
                <TR>
                    <TD colspan="8"><TABLE border="0" width="100%">
                            <TR>
                                <TD valign="middle" width="50"><img src="/images/prototype/listings/map.jpg"> </TD>
                                <TD> G/159 </TD>
                                <TD> Organisation: <a href="">FragSoc</a> </TD>
                                <TD> Section: <a href="">Speakers </a> </TD>
                                <TD width="60" style="cursor:pointer; " onClick="showEventMore('event',2);"><img src="/images/prototype/listings/more.gif"></TD>
                            </TR>
                        </TABLE></TD>
                </TR>
            </TABLE>
            <DIV id="event2" style="display: none;">
                <table width="630" height="100" style="border-width: 1; border-top-width: 0; border-style: dashed;  border-color: #06a5cd;">
                    <TR>
                        <TD valign="top"><P>David Hume's views on aesthetic theory and the philosophy of art are to be found in his work on moral theory and in several essays. Although there is a tendency to emphasize the two essays devoted to art, Of the Standard of Taste and Of Tragedy, his views on art and aesthetic judgment are intimately connected to his moral philosophy and theories of human thought and emotion. His theory of taste and beauty is not entirely original, but his arguments generally display the keen analysis typical of his best work. Hume's archaic terminology is occasionally an obstacle to appreciating his analysis, inviting conflicting readings of his position.</P></TD>
                    </TR>
                </TABLE>
            </DIV>
            <br>
            <h3>Friday 10th November</h3>
            <table width="630" style="border-width: 1; border-style:solid; border-color: #06a5cd;">
                <TR>
                    <TD width="130"> 1:15pm - 2:15pm </TD>
                    <TD width="296"><b>Once Hour Unreal Tournament</b> </TD>
                    <TD width="20" align="center"><img src="/images/prototype/listings/flag.gif"> </TD>
                    <TD width="20" align="center"><img src="/images/prototype/listings/todo.gif"> </TD>
                    <TD width="47" align="center"><img src="/images/prototype/listings/rsvp.gif"> </TD>
                    <TD width="22" align="center"><img src="/images/prototype/listings/email.jpg" width="19" height="19"> </TD>
                    <TD width="22" align="center"><a href="http://www.theyorker.co.uk/"> <img src="/images/prototype/listings/link.png" border="0"> </a></TD>
                    <TD width="73" align="center"><img src="/images/prototype/listings/subscribe.jpg"></TD>
                </TR>
                <TR>
                    <TD colspan="8">
						<TABLE border="0" width="100%">
                            <TR>
                                <TD valign="middle" width="50"><img src="/images/prototype/listings/map.jpg"> </TD>
                                <TD> G/159 </TD>
                                <TD> Organisation: <a href="">FragSoc</a> </TD>
                                <TD> Section: <a href="">Speakers </a> </TD>
                                <TD width="60" style="cursor:pointer; " onClick="showEventMore('event',1);"><img src="/images/prototype/listings/more.gif"></TD>
                            </TR>
							<TR>
								<TD bgcolor="#FF6A00" valign="middle" width="50" align="center"  style="border-top-style: dotted; border-top-width: 1; border-top-color: #FF6A00;"><font color="white"><b>Yorker Preview</b></font></td>
								<TD valign="middle" colspan="4" height="50"  style="border-top-style: dotted; border-top-width: 1; border-bottom-style: dotted; border-bottom-width: 1; border-color: #FF6A00;"><P style="font-size: small;">There isn't some clear-cut formula for making a great game, but Epic hasn't ignored bullet-point features in expanding the Unreal Tournament series, which is now in its third installment. The multiplayer-focused first-person shooter series started off in 1999 with great graphics, crisp control, solid networking (which led to smooth online gameplay), and hectic action.</P></TD>
							</TR>					
                        </TABLE></TD>
                </TR>
            </TABLE>
            <DIV id="event1" style="display: none;">
                <table width="630" height="100" style="border-width: 1; border-top-width: 0; border-style: dashed;  border-color: #06a5cd;">
                    <TR>
                        <TD valign="top"><P> Philosophy of language is the branch of philosophy whose primary concerns include the natures of meaning, reference, truth, language learning, language creation, understanding, communication, interpretation, and translation.<br>
                                <br>
                                The discipline is concerned with five central questions: How are sentences composed into a meaningful whole, and what are the meanings of the parts of sentences? What is the nature of meaning? (i.e., What exactly is a meaning?) What do we do with language? (How do we use it socially?) How does language relate to the mind, both of the speaker and the interpreter? Finally, how does language relate to the world? </P></TD>
                    </TR>
                </TABLE>
            </DIV>
<?php
}

// The rest of the view is the dynamic bit, the individual events look wrong coz
// i just copied them from listings. --jh559 13/12/2006

// Put special headings at top
foreach ($dayinfo as $id => $info) {
	$eventBoxCode[$id] = '';
	$dayempty[$id] = TRUE;
	foreach ($info['special_headings'] as $name) {
		$eventBoxCode[$id] .= '<div class="calviewEBCSpecialDayHeading">' .
				$name . '</div>';
	}
}

// Then events
foreach ($events as $events_array_index => $event) {
	
	$replace = array (
		'%%arrid%%' => $events_array_index,
		'%%refid%%' => $event['ref_id'],
		'%%name%%' => $event['name'],
		'%%date%%' => $event['date'],
		'%%day%%' =>  $event['day'],
		'%%starttime%%' => $event['starttime'],
		'%%endtime%%' => $event['endtime'],
		'%%blurb%%' => $event['blurb'],
		'%%shortloc%%' => $event['shortloc'],
		'%%type%%' => $event['type'],
	);
	
	$mypath = pathinfo(__FILE__);
	$snippets_dir = $mypath['dirname'] . "/snippets";
	@$eventBoxCode[$event['day']] .= apinc ($snippets_dir . "/calviewEventBox.inc",$replace);
	$dayempty[$event['day']] = FALSE;
	
}

// put &nbsp; onto end of all days
for ($i = 0;$i < 7;$i++) {
	@$eventBoxCode[$i] .= '&nbsp;';
}


?>

		<div id="calviewEventMenu" style="display: none">
			<ul>
				<li>
					<div class="calviewEMBP">
					<a href="#"	onclick="hideEventMenu(); 
					eventSetHighlight();return false;">Highlight</a>
					</div>
				</li>
				<li>
					<div class="calviewEMBP">
					<a href="#" onclick="hideEventMenu();
					return false;">View Full Details</a>
					</div>
				</li>
				<li>
					<div class="calviewEMBP">
					<a href="#" onclick="hideEventMenu()
					return false;">Display Options</a>
					</div>
				</li>
				<li>
					<div class="calviewEMBP">
					<a href="#" onclick="hideEventMenu()
					removeEvent;return false;">Hide Event</a>
					</div>
				</li>
				<li>
					<div class="calviewEMBP">
					<a href="#" onclick="hideEventMenu()
					return false;">List Similar Events</a>
					</div>
				</li>
				<li>
					<div class="calviewEMBP" style="border-bottom: none">
					<a href="#" onclick="hideEventMenu();return false;">Cancel</a>
					</div>
				</li>
			</ul>
		</div>
		
		

<?php
$pre_ac_title = '';
foreach ($dayinfo as $id => $info) {
	if (!$dayempty[$id]) {
		$ac_title =
			'week '.$info['academic_week' ].
			' of the '.$info['academic_term' ].
			' '.$info['academic_year' ];
		if ($ac_title !== $pre_ac_title) {
			echo '<H3>'.$ac_title.'</H3>'."\n";
			$pre_ac_title = $ac_title;
		}
		$title =
			$info['day_of_week'   ].
			' '.$info['date_and_month'];
		echo '<H4>'.$title.'</H4>'."\n";
		echo $eventBoxCode[$id];
	}
}
?>
	
	
</div>
<script type="text/javascript">
<?php echo $eventHandlerJS ?>
//Event.observe(document, "onmouseover", function (e) { hideEventMenu(e); });
</script>
