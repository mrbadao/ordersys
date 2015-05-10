$.datepicker.regional['ja'] = {                
            closeText: '閉じる',
            prevText: '&#x3c;前',
            nextText: '次&#x3e;',
            currentText: '今日',
            monthNames: ['1月','2月','3月','4月','5月','6月',
            '7月','8月','9月','10月','11月','12月'],
            monthNamesShort: ['1月','2月','3月','4月','5月','6月',
            '7月','8月','9月','10月','11月','12月'],
            dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
            dayNamesShort: ['日','月','火','水','木','金','土'],
            dayNamesMin: ['日','月','火','水','木','金','土'],
            weekHeader: '週',
            showOtherMonths: true,
            dateFormat : "yy-mm-dd"
       };
	   $.datepicker.setDefaults($.datepicker.regional['ja']);
	   
$.timepicker.regional['ja'] = {
			timeOnlyTitle: 'Choose Time',
			timeText: '時間',
			hourText: '時',
			minuteText: '分',
			secondText: '秒',
			millisecText: 'ミリセカント',
			timezoneText: 'タイムゾーン',
			currentText: '現在時刻',
			closeText: '閉じる',
			timeFormat: 'HH:mm',
			amNames: ['AM', 'A'],
			pmNames: ['PM', 'P'],
			isRTL: false
		};
		$.timepicker.setDefaults($.timepicker.regional['ja']);
		
		$( ".btn-datepicker" ).datepicker();
		$( ".btn-datetimepicker" ).datetimepicker();
		// set date ("setDate", new Date())
	
$('.btn-clearDate .close').click(function() {
		var _this = $(this);
		var _datepk = _this.prev(".btn-datepicker");
		var _dateTimepk = _this.prev(".btn-datetimepicker");
		_datepk.datepicker("setDate", null);
		_dateTimepk.datetimepicker("setDate", null);
	})
	
function newWindow(URL,Winname,Wwidth,Wheight){
    var WIN;
    WIN = window.open(URL,Winname,"width="+Wwidth+",height="+Wheight+",scrollbars=no,resizable=no,toolbar=no,location=no,directories=no,status=no");
    WIN.focus();
}