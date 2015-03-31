initDatepicker = function(id, format, translation) {

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
		onSelect: function(value) {
			document.getElementById(id).value = moment(value).format("X");
		}
	});

	if(document.getElementById(id + "-datepicker").addEventListener) {
		document.getElementById(id + "-datepicker").addEventListener("change", function() {
			updateDatepicker(id);	
		});
	} else {
		document.getElementById(id + "-datepicker").attachEvent("onchange", function() {
			updateDatepicker(id);	
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
