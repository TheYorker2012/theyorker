					<div id="ev_%%refid%%" class="calviewIndEventBox">
					<div>
						<div id="calviewIECtrlButtonBLBound">
						
							<div class="calviewCloseButton" onclick="removeEvent(%%refid%%);hideEventMenu()">
								<a href="#" onclick="return false"
								style="text-decoration: none; color: #ffffff;">X</a>
							</div>
							
<!--    						<div class="calviewMenuButton" id="ev_mb_%%refid%%" onclick="eventMenu()">
								<a href="#" onclick="return false"
								style="text-decoration: none; color: #ffffff;">&gt;</a>
							</div>
							-->
							<div class="calviewExpandButton"
								onmouseover="expandEvent(%%refid%%)"
								onmouseout="collapseEvent(%%refid%%)"
								style="cursor: pointer">
								V
							</div>
							
						</div>
						
						<strong>%%name%%</strong>
						
						<div class="calviewExpandedSmall" id="ev_es_%%refid%%" style="display: none">
						<div>
							%%starttime%% to %%endtime%%<br />At: %%shortloc%%<br /><i>%%blurb%%</i>
						</div>
						</div>
						
					</div>
					</div>
					<script type="text/javascript">
						$('ev_mb_%%refid%%').onclick="eventMenu()";
					</script>
