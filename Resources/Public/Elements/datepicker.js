initDatepicker = function(id, format, translation, configuration) {

	var minDate = -1, maxDate = -1, firstDay = 0;
	var currentYear = new Date().getFullYear();
	var yearRange = [currentYear - 10, currentYear];
	var disableDays = undefined, landingDate = undefined;

	if(configuration['minDate'] !== undefined)
		minDate = configuration['minDate'];
	if(configuration['maxDate'] !== undefined)
		maxDate = configuration['maxDate'];
	if(configuration['disableDays'] !== undefined)
		disableDays = configuration['disableDays'];
	if(configuration['landingDate'] !== undefined)
	{
		landingDate = configuration['landingDate'];
		yearRange = [landingDate.getFullYear() - 10, currentYear];
	}
	if(configuration['firstDay'] !== undefined)
		firstDay = configuration['firstDay'];
	if(configuration['yearRange'] !== undefined)
		yearRange = configuration['yearRange'];

	var datepicker = new Pikaday({
		field: document.getElementById(id + "-datepicker"),
		format: format,
		i18n: {
			previousMonth: i18n.previousMonth,
			nextMonth: i18n.nextMonth,
			months : [
				translation.months[1],
				translation.months[2],
				translation.months[3],
				translation.months[4],
				translation.months[5],
				translation.months[6],
				translation.months[7],
				translation.months[8],
				translation.months[9],
				translation.months[10],
				translation.months[11],
				translation.months[12]
			],
			weekdays : [
				i18n.weekdays[1],
				i18n.weekdays[2],
				i18n.weekdays[3],
				i18n.weekdays[4],
				i18n.weekdays[5],
				i18n.weekdays[6],
				i18n.weekdays[7]
			],
			weekdaysShort : [
				i18n.weekdaysShort[1],
				i18n.weekdaysShort[2],
				i18n.weekdaysShort[3],
				i18n.weekdaysShort[4],
				i18n.weekdaysShort[5],
				i18n.weekdaysShort[6],
				i18n.weekdaysShort[7]
			]
		},
		firstDay: firstDay,
		yearRange: yearRange,
		minDate: minDate,
		maxDate: maxDate,
		// Callback on each day created, return true to disable it
		disableDayFn: function(date) {
			if(disableDays !== undefined)
			{
				var isDisabled = false;
				if(disableDays['y'] !== undefined)
				{
					disableDays['y'].forEach(function(year){
						if(date.getFullYear() === year)
							isDisabled = true;
					});
					if(isDisabled)
						return isDisabled;
				}
				if(disableDays['m'] !== undefined)
				{
					disableDays['m'].forEach(function(month){
						if(date.getMonth() === month)
							isDisabled = true;
					});
					if(isDisabled)
						return true;
				}
				if(disableDays['d'] !== undefined)
				{
					disableDays['d'].forEach(function(day){
						if(date.getDay() === day)
							isDisabled = true;
					});
					if(isDisabled)
						return true;
				}
				if(disableDays['ts'] !== undefined)
				{
					disableDays['ts'].forEach(function(ts){
						var tsDate = new Date(ts);
						if(date.getFullYear().toString() + date.getMonth().toString() + date.getDate().toString() === tsDate.getFullYear().toString() + tsDate.getMonth().toString() + tsDate.getDate().toString())
							isDisabled = true;
					});
					if(isDisabled)
						return true;
				}
			}
		},
		onSelect: function(value) {
			document.getElementById(id).value = moment(value).format("X");
		}
	});

	if(landingDate !== undefined)
	{
        datepicker.gotoYear(landingDate.getFullYear().toString());
        datepicker.gotoMonth(landingDate.getMonth().toString());
	}

	if(document.getElementById(id + "-datepicker").addEventListener) {
		document.getElementById(id + "-datepicker").addEventListener("change", function() {
			updateDatepicker(id, format);	
		});
	} else {
		document.getElementById(id + "-datepicker").attachEvent("onchange", function() {
			updateDatepicker(id, format);	
		});
	}
};

updateDatepicker = function(id, format) {
	if(document.getElementById(id + "-datepicker").value == "") {
		document.getElementById(id).value = "";
	} else {
		document.getElementById(id).value = moment(document.getElementById(id + "-datepicker").value, format).format("X");
	}
};
