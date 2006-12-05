					<div id="ev_%%arrid%%" class="calviewIndEventBox">
					<div>
						<div id="calviewIECtrlButtonBLBound">
						
							<div class="calviewCloseButton" onclick="removeEvent(%%arrid%%);hideEventMenu()">
								<a href="#" onclick="return false"
								style="text-decoration: none; color: #ffffff;">X</a>
							</div>
							
							<div class="calviewMenuButton" id="ev_mb_%%arrid%%" onclick="eventMenu()">
								<a href="#" onclick="return false"
								style="text-decoration: none; color: #ffffff;">&gt;</a>
							</div>
							
							<div class="calviewExpandButton"
								onmouseover="expandEvent(%%arrid%%)"
								onmouseout="collapseEvent(%%arrid%%)"
								style="cursor: pointer">
								V
							</div>
							
						</div>
						
						<strong>%%name%%</strong>
						
						<div class="calviewExpandedSmall" id="ev_es_%%arrid%%" style="display: none">
						<div>
							%%starttime%% to %%endtime%%<br />At: %%location%%<br /><i>%%blurb%%</i>
						</div>
						</div>
						
					</div>
					</div>
					<script type="text/javascript">
						$('ev_mb_%%arrid%%').onclick=eventMenu;
					</script>
