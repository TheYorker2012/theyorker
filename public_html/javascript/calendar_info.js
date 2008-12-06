var lastOpenCalendarId = -1;

function changeOpenCalendarSelection(id, defaultId)
{
	if (-1 == lastOpenCalendarId) {
		lastOpenCalendarId = defaultId;
	}
	if (id != lastOpenCalendarId) {
		var prevDescription = document.getElementById('open_cal_desc_' + lastOpenCalendarId);
		if (null != prevDescription) {
			prevDescription.style.display = 'none';
		}
		lastOpenCalendarId = id;
		var newDescription = document.getElementById('open_cal_desc_' + id);
		if (null != newDescription) {
			newDescription.style.display = '';
		}
	}
}
